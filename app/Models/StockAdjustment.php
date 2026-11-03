<?php

namespace App\Models;

use App\Helpers\BsDateHelper;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockAdjustment extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'adjustment_number',
        'fiscal_year_id',
        'warehouse_id',
        'adjustment_date_ad',
        'adjustment_date_bs',
        'type',
        'status',
        'total_variance_value',
        'reason',
        'created_by',
        'approved_by',
        'approved_at',
        'rejection_reason',
    ];

    protected $casts = [
        'adjustment_date_ad' => 'date',
        'total_variance_value' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->adjustment_date_bs) && ! empty($model->adjustment_date_ad)) {
                $model->adjustment_date_bs = BsDateHelper::toBs($model->adjustment_date_ad);
            }
        });
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function lines()
    {
        return $this->hasMany(StockAdjustmentLine::class);
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }
}
