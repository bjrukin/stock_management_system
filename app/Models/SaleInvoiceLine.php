<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleInvoiceLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_invoice_id',
        'product_id',
        'batch_id',
        'location_id',
        'unit_id',
        'product_code',
        'product_name',
        'batch_number',
        'expiry_date_ad',
        'expiry_date_bs',
        'unit_name',
        'qty',
        'conversion_factor',
        'base_qty',
        'unit_price',
        'discount_pct',
        'discount_amount',
        'tax_category_id',
        'tax_rate',
        'tax_amount',
        'line_total',
        'unit_cost',
        'cogs_amount',
    ];

    protected $casts = [
        'expiry_date_ad' => 'date',
        'qty' => 'decimal:4',
        'conversion_factor' => 'decimal:4',
        'base_qty' => 'integer',
        'unit_price' => 'decimal:4',
        'discount_pct' => 'decimal:4',
        'discount_amount' => 'decimal:2',
        'tax_rate' => 'decimal:4',
        'tax_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
        'unit_cost' => 'decimal:4',
        'cogs_amount' => 'decimal:2',
    ];

    public function saleInvoice()
    {
        return $this->belongsTo(SaleInvoice::class);
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

    public function taxCategory()
    {
        return $this->belongsTo(TaxCategory::class);
    }
}
