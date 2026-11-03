<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicantLog extends Model {
    protected $fillable = [
        'professional_id',
        'admin_id',
        'applicant_id',
        'remarks',
        'state',
        'status',
    ];

    public function applicant() {
        return $this->belongsTo(Applicant::class, 'applicant_id', 'id');
    }
    
    public function professional() {
        return $this->belongsTo(Professional::class, 'professional_id', 'id');
    }
    
    public function admin() {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }
}
