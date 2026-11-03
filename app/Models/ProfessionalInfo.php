<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfessionalInfo extends Model
{
    protected $fillable = [
        'professional_id', 
	'program_id', 
	'level_id', 
	'first_name', 
	'middle_name', 
	'last_name', 
	'first_name_nep', 
	'middle_name_nep', 
	'last_name_nep', 
	'dob_eng', 
	'dob_nep', 
	'gender', 
	'marital_status', 
	'ethinic', 
	'citizenship_number', 
	'citizenship_issue_date', 
	'citizenship_issue_district', 
	'father_name', 
	'father_name_nep', 
	'grandfather_name', 
	'grandfather_name_nep', 
	'mother_name', 
	'mother_name_nep', 
	'province_id', 
	'district_id', 
	'municipality_id', 
	'ward_no', 
	'profile_picture', 
	'citizenship_front', 
	'citizenship_back', 
	'signature_image', 
	'profile_state', 
    ];

    protected $appends = ['full_name', 'full_profile_picture', 'full_citizenship_front', 'full_citizenship_back', 'full_signature_image'];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name;
    }

    public function getFullProfilePictureAttribute()
    {
        return asset('storage/uploads/student/' . $this->profile_picture);
    }

    public function getFullCitizenshipFrontAttribute()
    {
        return asset('storage/uploads/student/' . $this->citizenship_front);
    }

    public function getFullCitizenshipBackAttribute()
    {
        return asset('storage/uploads/student/' . $this->citizenship_back);
    }

    public function getFullSignatureImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->signature_image);
    }

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id', 'id');
    }


    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'id');
    }


    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }
    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id', 'id');
    }
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'id');
    }
    public function municipality()
    {
        return $this->belongsTo(Municipality::class, 'municipality_id', 'id');
    }
}
