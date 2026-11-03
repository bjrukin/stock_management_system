<?php

namespace App\Models;

use App\Helpers\BsDateHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountingPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiscal_year_id',
        'name',
        'period_number',
        'start_date_ad',
        'start_date_bs',
        'end_date_ad',
        'end_date_bs',
        'is_closed',
        'closed_by',
        'closed_at',
    ];

    protected $casts = [
        'start_date_ad' => 'date',
        'end_date_ad' => 'date',
        'is_closed' => 'boolean',
        'closed_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function (self $model) {
            if ($model->start_date_ad && empty($model->start_date_bs)) {
                $model->start_date_bs = BsDateHelper::toBs($model->start_date_ad);
            }
            if ($model->end_date_ad && empty($model->end_date_bs)) {
                $model->end_date_bs = BsDateHelper::toBs($model->end_date_ad);
            }
        });
    }

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }

    public function closedBy()
    {
        return $this->belongsTo(Admin::class, 'closed_by');
    }

    public static function isDateClosed(string|\Carbon\Carbon $dateAd): bool
    {
        $d = is_string($dateAd) ? $dateAd : $dateAd->format('Y-m-d');
        return static::where('start_date_ad', '<=', $d)
            ->where('end_date_ad', '>=', $d)
            ->where('is_closed', true)
            ->exists();
    }
}
