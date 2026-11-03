<?php

namespace App\Models;

use App\Helpers\BsDateHelper;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleReturn extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'return_number',
        'fiscal_year_id',
        'sale_invoice_id',
        'customer_id',
        'warehouse_id',
        'return_date_ad',
        'return_date_bs',
        'subtotal',
        'tax_amount',
        'total_amount',
        'reason',
        'credit_note_issued',
        'status',
        'posted_by',
        'posted_at',
        'created_by',
    ];

    protected $casts = [
        'return_date_ad' => 'date',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'credit_note_issued' => 'boolean',
        'posted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function (self $model) {
            if ($model->return_date_ad && empty($model->return_date_bs)) {
                $model->return_date_bs = BsDateHelper::toBs($model->return_date_ad);
            }
        });
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function saleInvoice()
    {
        return $this->belongsTo(SaleInvoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function lines()
    {
        return $this->hasMany(SaleReturnLine::class);
    }

    public function poster()
    {
        return $this->belongsTo(Admin::class, 'posted_by');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
