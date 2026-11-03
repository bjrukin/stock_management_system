<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = ['name', 'short_name_english', 'short_name_nepali', 'code', 'order', 'route_name', 'status', ];
}
