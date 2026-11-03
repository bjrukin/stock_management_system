<?php

namespace App\Models;

use App\Helpers\BsDateHelper;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'po_number',
        'fiscal_year_id',
        'supplier_id',
        'warehouse_id',
        'order_date_ad',
        'order_date_bs',
        'expected_delivery_date_ad',
        'expected_delivery_date_bs',
        'status',
        'subtotal',
        'discount_amount',
        'taxable_amount',
        'tax_amount',
        'total_amount',
        'notes',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'order_date_ad' => 'date',
        'expected_delivery_date_ad' => 'date',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function (self $model) {
            if ($model->order_date_ad && empty($model->order_date_bs)) {
                $model->order_date_bs = BsDateHelper::toBs($model->order_date_ad);
            }
            if ($model->expected_delivery_date_ad && empty($model->expected_delivery_date_bs)) {
                $model->expected_delivery_date_bs = BsDateHelper::toBs($model->expected_delivery_date_ad);
            }
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

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function lines()
    {
        return $this->hasMany(PurchaseOrderLine::class);
    }

    public function goodsReceipts()
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function approver()
    {
        return $this->belongsTo(Admin::class, 'approved_by');
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
