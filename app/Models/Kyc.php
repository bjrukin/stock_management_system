<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kyc extends Model
{
    protected $fillable = ['professional_id', 
	'name', 
	'dob', 
	'registration_number', 
	'symbol_number', 
	'profile_img', ];

    protected $appends = ['full_profile_img'];

    public function getFullProfileImgAttribute()
    {
        return asset('storage/uploads/student/'.$this->profile_img);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id', 'id');
    }
}
