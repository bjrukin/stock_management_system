<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipality extends Model
{
    protected $fillable = ['district_id', 'name', 'status' ];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    } 
}
