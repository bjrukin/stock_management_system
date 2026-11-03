<?php

namespace App\Models;

use App\Helpers\BsDateHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchRecall extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id',
        'recall_reference',
        'issued_by',
        'recall_date_ad',
        'recall_date_bs',
        'reason',
        'action_taken',
        'quarantined_quantity',
        'status',
        'created_by',
        'resolved_by',
        'resolved_at',
    ];

    protected $casts = [
        'recall_date_ad' => 'date',
        'quarantined_quantity' => 'integer',
        'resolved_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->recall_date_bs) && ! empty($model->recall_date_ad)) {
                $model->recall_date_bs = BsDateHelper::toBs($model->recall_date_ad);
            }
        });
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function creator()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function resolver()
    {
        return $this->belongsTo(Admin::class, 'resolved_by');
    }
}
