<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model {
    protected $fillable = [
    'professional_id', 
	'printed_by', 
	'issued_by', 
	'level_id', 
	'program_id', 
	'registration_id', 
	'srn', 
	'program_certificate_code', 
	'cert_registration_number', 
	'registrar', 
	'name', 
	'date_of_birth', 
	'address', 
	'qualification', 
	'decision_date', 
	'issued_year', 
	'issued_date', 
	'valid_till', 
	'certificate', 
	'type', 
	'remarks', 
	'is_printed', 
	'printed_date', 
	'is_edited', 
	'status', 
    ];
    public function professional() {
        return $this->belongsTo(Professional::class, 'professional_id', 'id');
    }

    public function program() {
        return $this->belongsTo(Program::class, 'program_id', 'id');
    }

    public function level() {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }
}