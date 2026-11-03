<?php

namespace App\Models;

use App\Helpers\BsDateHelper;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleInvoice extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'invoice_number',
        'fiscal_year_id',
        'customer_id',
        'customer_name',
        'customer_pan',
        'warehouse_id',
        'invoice_date_ad',
        'invoice_date_bs',
        'salesperson_id',
        'payment_method',
        'subtotal',
        'discount_amount',
        'taxable_amount',
        'non_taxable_amount',
        'tax_amount',
        'delivery_charge',
        'round_off',
        'total_amount',
        'paid_amount',
        'credit_amount',
        'cogs_amount',
        'status',
        'posted_by',
        'posted_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'invoice_date_ad' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'non_taxable_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'delivery_charge' => 'decimal:2',
        'round_off' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'cogs_amount' => 'decimal:2',
        'posted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function (self $model) {
            if ($model->invoice_date_ad && empty($model->invoice_date_bs)) {
                $model->invoice_date_bs = BsDateHelper::toBs($model->invoice_date_ad);
            }
        });
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function salesperson()
    {
        return $this->belongsTo(Admin::class, 'salesperson_id');
    }

    public function lines()
    {
        return $this->hasMany(SaleInvoiceLine::class);
    }

    public function poster()
    {
        return $this->belongsTo(Admin::class, 'posted_by');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }
}
