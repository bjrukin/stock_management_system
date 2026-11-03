<?php

namespace App\Models;

use App\Helpers\BsDateHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FiscalYear extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'start_date_ad',
        'start_date_bs',
        'end_date_ad',
        'end_date_bs',
        'is_current',
        'is_closed',
    ];

    protected $casts = [
        'start_date_ad' => 'date',
        'end_date_ad' => 'date',
        'is_current' => 'boolean',
        'is_closed' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function (self $model) {
            // Auto-populate BS dates from AD dates if missing
            if ($model->start_date_ad && empty($model->start_date_bs)) {
                $model->start_date_bs = BsDateHelper::toBs($model->start_date_ad);
            }
            if ($model->end_date_ad && empty($model->end_date_bs)) {
                $model->end_date_bs = BsDateHelper::toBs($model->end_date_ad);
            }
        });
    }

    /**
     * Get the active current fiscal year.
     */
    public static function current(): ?self
    {
        return self::where('is_current', true)->first();
    }

    public function accountingPeriods()
    {
        return $this->hasMany(AccountingPeriod::class);
    }

    public function documentSequences()
    {
        return $this->hasMany(DocumentSequence::class);
    }
}
