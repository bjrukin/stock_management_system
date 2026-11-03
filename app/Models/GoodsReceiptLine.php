<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsReceiptLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'goods_receipt_id',
        'purchase_order_line_id',
        'product_id',
        'batch_id',
        'batch_number',
        'mfg_date_ad',
        'mfg_date_bs',
        'expiry_date_ad',
        'expiry_date_bs',
        'location_id',
        'unit_id',
        'received_qty',
        'conversion_factor',
        'received_base_qty',
        'rejected_base_qty',
        'unit_cost',
        'unit_cost_base',
        'mrp',
        'selling_price',
        'wholesale_price',
        'discount_pct',
        'discount_amount',
        'tax_category_id',
        'tax_rate',
        'tax_amount',
        'line_total',
        'landed_cost_share',
        'effective_unit_cost',
    ];

    protected $casts = [
        'mfg_date_ad' => 'date',
        'expiry_date_ad' => 'date',
        'received_qty' => 'decimal:4',
        'conversion_factor' => 'decimal:4',
        'received_base_qty' => 'integer',
        'rejected_base_qty' => 'integer',
        'unit_cost' => 'decimal:4',
        'unit_cost_base' => 'decimal:4',
        'mrp' => 'decimal:4',
        'selling_price' => 'decimal:4',
        'wholesale_price' => 'decimal:4',
        'discount_pct' => 'decimal:4',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'landed_cost_share' => 'decimal:2',
        'effective_unit_cost' => 'decimal:4',
    ];

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function purchaseOrderLine()
    {
        return $this->belongsTo(PurchaseOrderLine::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function location()
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
