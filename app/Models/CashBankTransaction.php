<?php

namespace App\Models;

use App\Helpers\BsDateHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class CashBankTransaction extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'fiscal_year_id',
        'cash_bank_account_id',
        'transaction_date_ad',
        'transaction_date_bs',
        'transaction_type',
        'reference_type',
        'reference_id',
        'reference_number',
        'deposit_amount',
        'withdrawal_amount',
        'running_balance',
        'payment_method',
        'cheque_number',
        'cheque_date',
        'bank_name',
        'status',
        'notes',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'transaction_date_ad' => 'date',
        'cheque_date' => 'date',
        'deposit_amount' => 'decimal:2',
        'withdrawal_amount' => 'decimal:2',
        'running_balance' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->transaction_date_bs) && ! empty($model->transaction_date_ad)) {
                $model->transaction_date_bs = BsDateHelper::toBs($model->transaction_date_ad);
            }
            if (empty($model->created_at)) {
                $model->created_at = now();
            }
        });

        // Strict append-only immutability guard
        static::updating(function () {
            throw new RuntimeException('Cash and Bank ledger transactions are immutable and cannot be updated.');
        });

        static::deleting(function () {
            throw new RuntimeException('Cash and Bank ledger transactions are immutable and cannot be deleted.');
        });
    }

    public function account()
    {
        return $this->belongsTo(CashBankAccount::class, 'cash_bank_account_id');
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
