<?php

namespace App\Models;

use App\Helpers\BsDateHelper;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoodsReceipt extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'grn_number',
        'fiscal_year_id',
        'purchase_order_id',
        'supplier_id',
        'warehouse_id',
        'supplier_invoice_number',
        'supplier_invoice_date_ad',
        'supplier_invoice_date_bs',
        'receipt_date_ad',
        'receipt_date_bs',
        'freight_amount',
        'handling_charges',
        'other_charges',
        'subtotal',
        'discount_amount',
        'taxable_amount',
        'tax_amount',
        'total_amount',
        'total_landed_cost',
        'status',
        'posted_by',
        'posted_at',
        'created_by',
        'notes',
        'attachment_path',
    ];

    protected $casts = [
        'supplier_invoice_date_ad' => 'date',
        'receipt_date_ad' => 'date',
        'freight_amount' => 'decimal:2',
        'handling_charges' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'taxable_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'total_landed_cost' => 'decimal:2',
        'posted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function (self $model) {
            if ($model->receipt_date_ad && empty($model->receipt_date_bs)) {
                $model->receipt_date_bs = BsDateHelper::toBs($model->receipt_date_ad);
            }
            if ($model->supplier_invoice_date_ad && empty($model->supplier_invoice_date_bs)) {
                $model->supplier_invoice_date_bs = BsDateHelper::toBs($model->supplier_invoice_date_ad);
            }
        });
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
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
        return $this->hasMany(GoodsReceiptLine::class);
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
