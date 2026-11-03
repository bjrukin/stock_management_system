<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'code',
        'name',
        'contact_person',
        'phone',
        'email',
        'pan_vat_number',
        'dda_license_number',
        'address',
        'credit_period_days',
        'credit_limit',
        'credit_stop',
        'opening_balance',
        'current_balance',
        'is_active',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'credit_stop' => 'boolean',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'credit_period_days' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function invoices()
    {
        return $this->hasMany(SaleInvoice::class);
    }

    public function ledgerEntries()
    {
        return $this->hasMany(CustomerLedgerEntry::class)->orderBy('entry_date_ad')->orderBy('id');
    }

    public function returns()
    {
        return $this->hasMany(SaleReturn::class);
    }

    /**
     * Check if customer can be issued additional credit.
     */
    public function canTakeCredit(float $additionalAmount): bool
    {
        if ($this->credit_stop) {
            return false;
        }

        // If credit limit is 0, no credit limit is enforced (unlimited)
        if ($this->credit_limit <= 0) {
            return true;
        }

        $projectedBalance = (float) bcadd((string) $this->current_balance, (string) $additionalAmount, 2);

        return $projectedBalance <= (float) $this->credit_limit;
    }
}
