<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleReturnLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_return_id',
        'sale_invoice_line_id',
        'product_id',
        'batch_id',
        'location_id',
        'unit_id',
        'returned_qty',
        'conversion_factor',
        'returned_base_qty',
        'condition',
        'unit_price',
        'tax_amount',
        'line_total',
        'restocked',
    ];

    protected $casts = [
        'returned_qty' => 'decimal:4',
        'conversion_factor' => 'decimal:4',
        'returned_base_qty' => 'integer',
        'unit_price' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'restocked' => 'boolean',
    ];

    public function saleReturn()
    {
        return $this->belongsTo(SaleReturn::class);
    }

    public function saleInvoiceLine()
    {
        return $this->belongsTo(SaleInvoiceLine::class);
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
