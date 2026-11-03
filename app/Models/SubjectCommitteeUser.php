<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectCommitteeUser extends Model
{
    protected $fillable = ['user_id', 
	'subject_committee_id', 
	'signature_image', 
	'coordinator',  ];
    public function subject_committee(){
        return $this->belongsTo(SubjectCommittee::class, 'subject_committee_id', 'id');
    }

    public function user(){
        return $this->belongsTo(Admin::class, 'user_id', 'id'); 
    }
}
