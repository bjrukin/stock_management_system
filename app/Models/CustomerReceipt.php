<?php

namespace App\Models;

use App\Helpers\BsDateHelper;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomerReceipt extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'receipt_number',
        'fiscal_year_id',
        'customer_id',
        'cash_bank_account_id',
        'receipt_date_ad',
        'receipt_date_bs',
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
        'receipt_date_ad' => 'date',
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
            if (empty($model->receipt_date_bs) && ! empty($model->receipt_date_ad)) {
                $model->receipt_date_bs = BsDateHelper::toBs($model->receipt_date_ad);
            }
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
        return $this->hasMany(CustomerReceiptAllocation::class);
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
