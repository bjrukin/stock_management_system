<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable {

    protected $fillable = ['name', 'phone', 'email', 'role_id', 'username', 'password', 'password_reference', 'status'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function role() {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function subject_committee_users() {
        return $this->hasMany(SubjectCommitteeUser::class, 'user_id', 'id');
    }

    public function warehouses() {
        return $this->belongsToMany(Warehouse::class, 'admin_warehouse')
            ->withPivot('is_default')
            ->withTimestamps();
    }

    public function defaultWarehouse() {
        return $this->warehouses()->wherePivot('is_default', true)->first()
            ?? $this->warehouses()->first();
    }

    public function hasWarehouseAccess($warehouseId) {
        if ($this->role_id == 1) {
            return true;
        }

        return $this->warehouses()->where('warehouses.id', $warehouseId)->exists();
    }

    public function hasPermission($moduleName, $permissionName) {

        if (! $this->role) {
            return false;
        }

        if ($this->role_id == 1) {
            return true;
        }

        return $this->role->permissions()
            ->whereRelation('module', 'name', $moduleName)
            ->whereJsonContains('name', $permissionName)
            ->exists();

        // ->whereHas('module', function ($q) use ($moduleName) {
        //     $q->where('name', $moduleName);
        // })
    }
}