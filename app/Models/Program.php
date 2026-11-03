<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    protected $fillable = ['name', 'certificate_name', 'code', 'qualification',  'level_id', 'subject_committee_id',  'program_duration', 'duration_type', 'program_type', 'has_exam', 'status' ];

    
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    } 

    public function subject_committee()
    {
        return $this->belongsTo(SubjectCommittee::class, 'subject_committee_id', 'id');
    } 

    public function applicant()
    {
        return $this->hasMany(Applicant::class, 'program_id', 'id');
    }

    public function admit_cards() {
        return $this->hasMany(Applicant::class, 'program_id', 'id')->where('is_admit_card_generate', 1);
    }

    public function pass_applicant() {
        return $this->hasMany(Applicant::class, 'program_id', 'id')->where('is_passed', 1);
    }

    public function fail_applicant() {
        return $this->hasMany(Applicant::class, 'program_id', 'id')->where('is_passed', 0)->where('is_admit_card_generate', 1);
    }
}
