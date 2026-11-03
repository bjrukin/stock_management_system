<?php

namespace App\Models;

use App\Helpers\BsDateHelper;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransfer extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'transfer_number',
        'fiscal_year_id',
        'from_warehouse_id',
        'to_warehouse_id',
        'transfer_date_ad',
        'transfer_date_bs',
        'status',
        'total_items',
        'notes',
        'created_by',
        'dispatched_by',
        'dispatched_at',
        'received_by',
        'received_at',
    ];

    protected $casts = [
        'transfer_date_ad' => 'date',
        'total_items' => 'integer',
        'dispatched_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->transfer_date_bs) && ! empty($model->transfer_date_ad)) {
                $model->transfer_date_bs = BsDateHelper::toBs($model->transfer_date_ad);
            }
        });
    }

    public function fromWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }

    public function toWarehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function lines()
    {
        return $this->hasMany(StockTransferLine::class);
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function dispatcher()
    {
        return $this->belongsTo(Admin::class, 'dispatched_by');
    }

    public function receiver()
    {
        return $this->belongsTo(Admin::class, 'received_by');
    }
}
