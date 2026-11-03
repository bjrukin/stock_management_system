<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {
    protected $fillable = ['role_id', 'module_id', 'name'];

    protected $casts = [
        'name' => 'array',
    ];

    public function role() {
        return $this->belongsTo(Role::class);
    }

    public function module() {
        return $this->belongsTo(Module::class);
    }

    public function hasPermission($role_id, $module_id, $permissionName) {
        return self::where(['role_id' => $role_id, 'module_id' => $module_id])->whereJsonContains('name', $permissionName)->exists();
    }
}
