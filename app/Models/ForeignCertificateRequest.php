<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ForeignCertificateRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'professional_id',
        'level_id',
        'program_id',
        'voucher_image',
        'status',
    ];

    protected $appends = ['full_profile_img'];

    public function professional()
    {
        return $this->belongsTo(Professional::class, 'professional_id', 'id');
    }
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }
    public function program()
    {
        return $this->belongsTo(Program::class, 'program_id', 'id');
    }
    public function getFullVoucherImageAttribute()
    {
        return asset('storage/uploads/student/' . $this->voucher_image);
    }

}
