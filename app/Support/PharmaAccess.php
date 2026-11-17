<?php

namespace App\Support;

use App\Models\Admin;
use App\Models\CustomerReceipt;
use App\Models\StockTransfer;
use App\Models\SupplierPayment;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;

class PharmaAccess
{
    public static function admin(): Admin
    {
        $admin = auth()->guard('admin')->user();
        abort_if($admin === null, 401);

        return $admin;
    }

    // null means unrestricted; an empty array deliberately means no warehouses.
    public static function warehouseIds(): ?array
    {
        $admin = static::admin();

        return (int) $admin->role_id === 1 ? null : $admin->warehouses()->pluck('warehouses.id')->all();
    }

    public static function scope($query, array $columns = ['warehouse_id'])
    {
        $ids = static::warehouseIds();
        if ($ids !== null) {
            foreach ($columns as $column) {
                $query->whereIn($column, $ids);
            }
        }

        return $query;
    }

    public static function warehouse($id): void
    {
        abort_unless(filter_var($id, FILTER_VALIDATE_INT) !== false && (int) $id > 0, 422);
        abort_unless(static::admin()->hasWarehouseAccess((int) $id), 403, 'You do not have access to this warehouse.');
    }

    public static function document(Model $document): void
    {
        if ($document instanceof Warehouse) {
            static::warehouse($document->id);
        } elseif ($document instanceof StockTransfer) {
            static::warehouse($document->from_warehouse_id);
            static::warehouse($document->to_warehouse_id);
        } elseif ($document->getAttribute('warehouse_id') !== null) {
            static::warehouse($document->warehouse_id);
        } elseif ($document instanceof CustomerReceipt || $document instanceof SupplierPayment) {
            $relation = $document instanceof CustomerReceipt ? 'invoice' : 'goodsReceipt';
            foreach ($document->allocations()->with($relation)->get() as $allocation) {
                abort_if($allocation->$relation === null, 403);
                static::document($allocation->$relation);
            }
        }
    }

    public static function payments($query, string $relation)
    {
        $ids = static::warehouseIds();
        if ($ids !== null) {
            $query->whereDoesntHave('allocations.'.$relation, fn ($q) => $q->whereNotIn('warehouse_id', $ids));
        }

        return $query;
    }
}
