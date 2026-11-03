<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseReturnLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_return_id',
        'goods_receipt_line_id',
        'product_id',
        'batch_id',
        'location_id',
        'unit_id',
        'returned_qty',
        'conversion_factor',
        'returned_base_qty',
        'unit_price',
        'tax_amount',
        'line_total',
    ];

    protected $casts = [
        'returned_qty' => 'decimal:4',
        'conversion_factor' => 'decimal:4',
        'returned_base_qty' => 'integer',
        'unit_price' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function goodsReceiptLine()
    {
        return $this->belongsTo(GoodsReceiptLine::class);
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
