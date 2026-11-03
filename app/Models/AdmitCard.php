<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmitCard extends Model
{
    protected $fillable = ['professional_id', 
	'applicant_id', 
	'symbol_number', 
	'is_taken', 
	'created_by', 
	'updated_by', ];

    public function applicant () {
        return $this->belongsTo(Applicant::class ,'applicant_id', 'id');
    }
}
