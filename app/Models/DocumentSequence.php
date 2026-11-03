<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentSequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'fiscal_year_id',
        'series',
        'prefix',
        'suffix',
        'last_number',
        'pad_length',
    ];

    protected $casts = [
        'last_number' => 'integer',
        'pad_length' => 'integer',
    ];

    public function fiscalYear()
    {
        return $this->belongsTo(FiscalYear::class);
    }
}
