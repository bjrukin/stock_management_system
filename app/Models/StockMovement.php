<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class StockMovement extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'fiscal_year_id',
        'warehouse_id',
        'location_id',
        'product_id',
        'batch_id',
        'movement_type',
        'quantity',
        'balance_after',
        'unit_cost',
        'total_cost',
        'reference_type',
        'reference_id',
        'from_location_id',
        'to_location_id',
        'created_by',
        'notes',
        'created_at',
    ];

    // Stock Process Inflow Types
    const TYPE_OPENING_STOCK = 'OPENING_STOCK';
    const TYPE_PURCHASE = 'PURCHASE_RECEIPT';
    const TYPE_SELL_RETURN = 'SALE_RETURN';

    // Stock Process Outflow Types
    const TYPE_SELL = 'SALE_DISPATCH';
    const TYPE_PURCHASE_RETURN = 'PURCHASE_RETURN';
    const TYPE_EXPIRED = 'EXPIRED_DISPOSAL';
    const TYPE_DAMAGE = 'DAMAGE_WRITE_OFF';

    // Internal Warehouse Operations
    const TYPE_TRANSFER_IN = 'TRANSFER_IN';
    const TYPE_TRANSFER_OUT = 'TRANSFER_OUT';
    const TYPE_RELOCATION = 'RELOCATION';
    const TYPE_QUARANTINE_MOVE = 'QUARANTINE_MOVE';

    protected $casts = [
        'quantity' => 'integer',
        'balance_after' => 'integer',
        'unit_cost' => 'decimal:4',
        'total_cost' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    /**
     * Enforce append-only ledger immutability.
     */
    protected static function booted()
    {
        static::updating(function () {
            throw new RuntimeException('Stock movements are immutable and cannot be updated.');
        });

        static::deleting(function () {
            throw new RuntimeException('Stock movements are immutable and cannot be deleted.');
        });
    }

    public function isInflow(): bool
    {
        return $this->quantity > 0;
    }

    public function isOutflow(): bool
    {
        return $this->quantity < 0;
    }

    public function getFlowDirectionAttribute(): string
    {
        return $this->quantity > 0 ? 'inflow' : 'outflow';
    }

    public function getFlowCategoryAttribute(): string
    {
        return match ($this->movement_type) {
            self::TYPE_PURCHASE => 'purchase',
            self::TYPE_SELL_RETURN => 'sell_return',
            self::TYPE_OPENING_STOCK => 'opening_stock',
            self::TYPE_SELL => 'sell',
            self::TYPE_PURCHASE_RETURN => 'purchase_return',
            self::TYPE_EXPIRED => 'expired_item',
            self::TYPE_DAMAGE => 'damage',
            default => 'other',
        };
    }

    public function getFlowLabelAttribute(): string
    {
        return match ($this->movement_type) {
            self::TYPE_PURCHASE => 'Purchase',
            self::TYPE_SELL_RETURN => 'Sell Return',
            self::TYPE_OPENING_STOCK => 'Opening Stock',
            self::TYPE_SELL => 'Sell',
            self::TYPE_PURCHASE_RETURN => 'Purchase Return',
            self::TYPE_EXPIRED => 'Expired Item',
            self::TYPE_DAMAGE => 'Damage',
            default => ucwords(strtolower(str_replace('_', ' ', $this->movement_type))),
        };
    }

    public function scopeInflows($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeOutflows($query)
    {
        return $query->where('quantity', '<', 0);
    }

    public function scopePurchases($query)
    {
        return $query->where('movement_type', self::TYPE_PURCHASE);
    }

    public function scopeSellReturns($query)
    {
        return $query->where('movement_type', self::TYPE_SELL_RETURN);
    }

    public function scopeSales($query)
    {
        return $query->where('movement_type', self::TYPE_SELL);
    }

    public function scopePurchaseReturns($query)
    {
        return $query->where('movement_type', self::TYPE_PURCHASE_RETURN);
    }

    public function scopeExpiredItems($query)
    {
        return $query->where('movement_type', self::TYPE_EXPIRED);
    }

    public function scopeDamages($query)
    {
        return $query->where('movement_type', self::TYPE_DAMAGE);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function location()
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function fromLocation()
    {
        return $this->belongsTo(WarehouseLocation::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(WarehouseLocation::class, 'to_location_id');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
