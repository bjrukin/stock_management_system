<?php

namespace App\Models;

use App\Helpers\BsDateHelper;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierPayment extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'payment_number',
        'fiscal_year_id',
        'supplier_id',
        'cash_bank_account_id',
        'payment_date_ad',
        'payment_date_bs',
        'payment_method',
        'amount',
        'allocated_amount',
        'unallocated_amount',
        'cheque_number',
        'cheque_date',
        'bank_name',
        'transaction_reference',
        'status',
        'created_by',
        'posted_by',
        'posted_at',
        'reversed_by',
        'reversed_at',
        'reversal_reason',
        'notes',
    ];

    protected $casts = [
        'payment_date_ad' => 'date',
        'cheque_date' => 'date',
        'amount' => 'decimal:2',
        'allocated_amount' => 'decimal:2',
        'unallocated_amount' => 'decimal:2',
        'posted_at' => 'datetime',
        'reversed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->payment_date_bs) && ! empty($model->payment_date_ad)) {
                $model->payment_date_bs = BsDateHelper::toBs($model->payment_date_ad);
            }
        });
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function account()
    {
        return $this->belongsTo(CashBankAccount::class, 'cash_bank_account_id');
    }

    public function allocations()
    {
        return $this->hasMany(SupplierPaymentAllocation::class);
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function poster()
    {
        return $this->belongsTo(Admin::class, 'posted_by');
    }

    public function reverser()
    {
        return $this->belongsTo(Admin::class, 'reversed_by');
    }
}
