<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Professional extends Authenticatable {
    protected $fillable = ['name',
        'phone',
        'email',
        'password_reference',
        'password',
        'otp',
        'is_foreign',
        'is_verify',
        'status'];

    protected $hidden = ['password'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function info() {
        return $this->hasOne(ProfessionalInfo::class, 'professional_id', 'id');
    }

    public function kyc() {
        return $this->hasOne(Kyc::class, 'professional_id', 'id');
    }

    public function qualifications()
    {
        return $this->hasMany(ProfessionalQualification::class, 'professional_id', 'id');
    }

    public function qualification()
    {
        return $this->hasOne(ProfessionalQualification::class, 'professional_id', 'id')->whereNotNull('college_name')->orderBy('id', 'desc');
    }

    public function slc()
    {
        return $this->hasOne(ProfessionalQualification::class, 'professional_id', 'id')->where('level_id', 5);
    }

    public function tslc()
    {
        return $this->hasOne(ProfessionalQualification::class, 'professional_id', 'id')->where('level_id', 4);
    }

    public function pcl()
    {
        return $this->hasOne(ProfessionalQualification::class, 'professional_id', 'id')->where('level_id', 3);
    }

    public function bachelor()
    {
        return $this->hasOne(ProfessionalQualification::class, 'professional_id', 'id')->where('level_id', 2);
    }
    public function master()
    {
        return $this->hasOne(ProfessionalQualification::class, 'professional_id', 'id')->where('level_id', 1);
    }

    public function applicants()
    {
        return $this->hasMany(Applicant::class, 'professional_id', 'id')->orderBy('id', 'desc');
    }



}