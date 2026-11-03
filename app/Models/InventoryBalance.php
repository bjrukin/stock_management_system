<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'warehouse_id',
        'location_id',
        'product_id',
        'batch_id',
        'quantity_on_hand',
        'quantity_reserved',
        'quantity_damaged',
        'quantity_quarantined',
        'quantity_in_transit_out',
    ];

    protected $casts = [
        'quantity_on_hand' => 'integer',
        'quantity_reserved' => 'integer',
        'quantity_damaged' => 'integer',
        'quantity_quarantined' => 'integer',
        'quantity_in_transit_out' => 'integer',
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function location()
    {
        return $this->belongsTo(WarehouseLocation::class, 'location_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    /**
     * Authoritative available quantity for sales/allocation:
     * Available = On-Hand - Reserved - Damaged - Quarantined - In-Transit-Out.
     */
    public function getQuantityAvailableAttribute(): int
    {
        $avail = $this->quantity_on_hand
            - $this->quantity_reserved
            - $this->quantity_damaged
            - $this->quantity_quarantined
            - $this->quantity_in_transit_out;

        return max(0, (int) $avail);
    }
}
