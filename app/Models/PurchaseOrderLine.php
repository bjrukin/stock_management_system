<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'product_id',
        'unit_id',
        'ordered_qty',
        'conversion_factor',
        'ordered_base_qty',
        'received_base_qty',
        'unit_price',
        'discount_pct',
        'discount_amount',
        'tax_category_id',
        'tax_rate',
        'tax_amount',
        'line_total',
    ];

    protected $casts = [
        'ordered_qty' => 'decimal:4',
        'conversion_factor' => 'decimal:4',
        'ordered_base_qty' => 'integer',
        'received_base_qty' => 'integer',
        'unit_price' => 'decimal:4',
        'discount_pct' => 'decimal:4',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function taxCategory()
    {
        return $this->belongsTo(TaxCategory::class);
    }

    public function getRemainingBaseQtyAttribute(): int
    {
        return max(0, (int) ($this->ordered_base_qty - $this->received_base_qty));
    }

    public function getIsFulfilledAttribute(): bool
    {
        return $this->received_base_qty >= $this->ordered_base_qty;
    }
}
