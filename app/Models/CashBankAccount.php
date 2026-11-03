<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashBankAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'type',
        'account_number',
        'bank_name',
        'branch',
        'opening_balance',
        'current_balance',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function transactions()
    {
        return $this->hasMany(CashBankTransaction::class)->orderBy('transaction_date_ad')->orderBy('id');
    }

    public function receipts()
    {
        return $this->hasMany(CustomerReceipt::class);
    }

    public function payments()
    {
        return $this->hasMany(SupplierPayment::class);
    }
}
