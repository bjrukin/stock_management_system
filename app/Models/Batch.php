<?php

namespace App\Models;

use App\Helpers\BsDateHelper;
use App\Traits\Auditable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Batch extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'product_id',
        'batch_number',
        'manufacturer_id',
        'supplier_id',
        'goods_receipt_id',
        'mfg_date_ad',
        'mfg_date_bs',
        'expiry_date_ad',
        'expiry_date_bs',
        'mrp',
        'cost_price',
        'selling_price',
        'wholesale_price',
        'is_quarantined',
        'is_recalled',
        'notes',
    ];

    protected $casts = [
        'mfg_date_ad' => 'date',
        'expiry_date_ad' => 'date',
        'mrp' => 'decimal:4',
        'cost_price' => 'decimal:4',
        'selling_price' => 'decimal:4',
        'wholesale_price' => 'decimal:4',
        'is_quarantined' => 'boolean',
        'is_recalled' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function (self $model) {
            // Auto-populate BS dates from AD dates if missing
            if ($model->mfg_date_ad && empty($model->mfg_date_bs)) {
                $model->mfg_date_bs = BsDateHelper::toBs($model->mfg_date_ad);
            }
            if ($model->expiry_date_ad && empty($model->expiry_date_bs)) {
                $model->expiry_date_bs = BsDateHelper::toBs($model->expiry_date_ad);
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function inventoryBalances()
    {
        return $this->hasMany(InventoryBalance::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeSellable($query)
    {
        return $query->where('is_quarantined', false)
            ->where('is_recalled', false)
            ->where('expiry_date_ad', '>', Carbon::today());
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date_ad', '<=', Carbon::today());
    }

    public function scopeNearExpiry($query, int $days = 90)
    {
        $today = Carbon::today();
        $threshold = Carbon::today()->addDays($days);

        return $query->where('expiry_date_ad', '>', $today)
            ->where('expiry_date_ad', '<=', $threshold);
    }

    public function scopeActive($query)
    {
        return $query->where('is_quarantined', false)
            ->where('is_recalled', false);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date_ad ? $this->expiry_date_ad->isPast() : false;
    }

    public function getStatusAttribute(): string
    {
        if ($this->is_recalled) {
            return 'recalled';
        }
        if ($this->is_quarantined) {
            return 'quarantined';
        }
        if ($this->is_expired) {
            return 'expired';
        }
        return 'active';
    }

    public function getDaysToExpiryAttribute(): int
    {
        if (! $this->expiry_date_ad) {
            return 999999;
        }

        return (int) Carbon::today()->diffInDays($this->expiry_date_ad, false);
    }
}
