<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WarehouseLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'warehouse_id',
        'parent_id',
        'code',
        'name',
        'type',
        'depth',
        'is_sellable',
        'is_available',
        'storage_condition',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'depth' => 'integer',
        'is_sellable' => 'boolean',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'capacity' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSellable($query)
    {
        return $query->where('is_sellable', true)->where('is_available', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function parent()
    {
        return $this->belongsTo(WarehouseLocation::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(WarehouseLocation::class, 'parent_id');
    }
}
