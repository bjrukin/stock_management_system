<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EligibilityMark extends Model
{
    protected $fillable = ['level_id', 
	'min_physics_marks', 
	'min_chemistry_marks', 
	'min_bio_marks', 
	'min_percentage', 
	'min_gpa', 
	'total_days_passport', 
	'total_days_internship'];

    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }
    public function program()
    {
        return $this->belongsTo(Level::class, 'program_id', 'id');
    }
}
