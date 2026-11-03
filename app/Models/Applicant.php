<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Applicant extends Model
{
    protected $fillable = [
        'exam_id',
        'professional_id',
        'voucher_image',
        'biometric_photo',
        'remarks',
        'subject_committee_count',
        'level_id',
        'program_id',
        'attempt',
        'is_admit_card_generate',
        'is_certificate_generate',
        'is_passed',
        'rejected',
        'payment_by',
        'payment_success',
        'state',
        'status',
    ];

    protected $appends = ['full_voucher_image'];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id', 'id');
    }
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'id');
    }
    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id', 'id');
    }

    public function professional_info()
    {
        return $this->belongsTo(ProfessionalInfo::class, 'professional_id', 'professional_id');
    }
    public function admit_card()
    {
        return $this->hasOne(AdmitCard::class, 'applicant_id', 'id');
    }
    public function applicant_logs()
    {
        return $this->hasMany(ApplicantLog::class, 'applicant_id', 'id');
    }
    public function getFullVoucherImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->voucher_image);
    }
    public function getFullBiometricPhotoAttribute()
    {
        return asset('storage/uploads/student/' . $this->biometric_photo);
    }

}
