<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    protected $fillable = ['exam_number', 
	'name', 
	'name_nep', 
	'opening_date', 
	'closing_date', 
	'exam_fee', 
	'late_fee_start_date', 
	'late_fee', 
	'description', 
	'admit_card_generated', 
	'result_published', 
	'status', ];
}
