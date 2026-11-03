<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationStep extends Model
{
    protected $fillable = ['role_id', 'step'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
