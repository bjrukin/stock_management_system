<?php

namespace App\Http\Middleware;

use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptLine;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderLine;
use App\Models\SaleInvoice;
use App\Models\WarehouseLocation;
use App\Support\PharmaAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PharmaAuthorize
{
    public function handle(Request $request, Closure $next): Response
    {
        $name = $request->route()->getName();
        // Route names contain dots; look them up as exact array keys.
        $rule = $name === 'admin.dashboard'
            ? ['module' => 'Report', 'action' => 'view']
            : (config('pharma.permissions')[substr($name ?? '', strlen('admin.pharma.'))] ?? null);
        abort_if($rule === null, 403);
        $admin = PharmaAccess::admin();
        abort_unless($admin->hasPermission($rule['module'], $rule['action']), 403);

        if ($request->input('action') === 'save_and_approve') {
            abort_unless($admin->hasPermission($rule['module'], 'approve'), 403);
        }
        if ($request->input('action') === 'save_and_post') {
            abort_unless($admin->hasPermission($rule['module'], 'post'), 403);
        }
        if ($name === 'admin.pharma.warehouse.store' && $request->boolean('is_primary')) {
            abort_unless((int) $admin->role_id === 1, 403, 'Changing the primary warehouse requires a super admin.');
        }
        if ($name === 'admin.pharma.reconcile') {
            abort_unless((int) $admin->role_id === 1, 403, 'Reconciliation requires a super admin.');
        }

        if (isset($rule['model'])) {
            $model = $rule['model']::findOrFail($request->route('id'));
            PharmaAccess::document($model);
            if ($name === 'admin.pharma.warehouse.update' && $request->boolean('is_primary') !== (bool) $model->is_primary) {
                abort_unless((int) $admin->role_id === 1, 403, 'Changing the primary warehouse requires a super admin.');
            }
        }
        $this->checkReferences($request->all(), $request);

        return $next($request);
    }

    private function checkReferences(array $values, Request $request): void
    {
        $documents = [
            'purchase_order_id' => PurchaseOrder::class, 'po_id' => PurchaseOrder::class,
            'goods_receipt_id' => GoodsReceipt::class, 'grn_id' => GoodsReceipt::class,
            'sale_invoice_id' => SaleInvoice::class,
        ];
        foreach ($values as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            if (in_array($key, ['warehouse_id', 'from_warehouse_id', 'to_warehouse_id'], true)) {
                PharmaAccess::warehouse($value);
            } elseif (in_array($key, ['location_id', 'from_location_id', 'to_location_id'], true)) {
                $this->validId($value);
                $location = WarehouseLocation::findOrFail($value);
                PharmaAccess::warehouse($location->warehouse_id);
                $warehouse = $request->input($key === 'from_location_id' ? 'from_warehouse_id' : ($key === 'to_location_id' ? 'to_warehouse_id' : 'warehouse_id'));
                abort_if($warehouse !== null && (int) $location->warehouse_id !== (int) $warehouse, 403, 'Location does not belong to the selected warehouse.');
            } elseif (isset($documents[$key])) {
                $this->validId($value);
                $document = $documents[$key]::findOrFail($value);
                PharmaAccess::document($document);
                $warehouse = $request->input('warehouse_id');
                abort_if($warehouse !== null && (int) $document->warehouse_id !== (int) $warehouse, 403);
            } elseif (in_array($key, ['purchase_order_line_id', 'goods_receipt_line_id'], true)) {
                $this->validId($value);
                $isOrder = $key === 'purchase_order_line_id';
                $line = ($isOrder ? PurchaseOrderLine::class : GoodsReceiptLine::class)::findOrFail($value);
                $parentId = $line->getAttribute($isOrder ? 'purchase_order_id' : 'goods_receipt_id');
                $document = ($isOrder ? PurchaseOrder::class : GoodsReceipt::class)::findOrFail($parentId);
                PharmaAccess::document($document);
                $selected = $request->input($isOrder ? 'purchase_order_id' : 'goods_receipt_id');
                abort_if($selected !== null && (int) $selected !== (int) $parentId, 403);
            } elseif (is_array($value)) {
                $this->checkReferences($value, $request);
            }
        }
    }

    private function validId($value): void
    {
        abort_unless(filter_var($value, FILTER_VALIDATE_INT) !== false && (int) $value > 0, 422);
    }
}
