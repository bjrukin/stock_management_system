<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class SupplierLedgerEntry extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'fiscal_year_id',
        'supplier_id',
        'entry_date_ad',
        'entry_date_bs',
        'transaction_type',
        'reference_type',
        'reference_id',
        'reference_number',
        'debit_amount',
        'credit_amount',
        'running_balance',
        'notes',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'entry_date_ad' => 'date',
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'running_balance' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::updating(function () {
            throw new RuntimeException('Supplier ledger entries are immutable and cannot be updated.');
        });

        static::deleting(function () {
            throw new RuntimeException('Supplier ledger entries are immutable and cannot be deleted.');
        });
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
