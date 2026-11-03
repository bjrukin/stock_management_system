<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPackagingLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'unit_id',
        'level_name',
        'level_order',
        'conversion_factor',
        'allow_fractional_sale',
    ];

    protected $casts = [
        'level_order' => 'integer',
        'conversion_factor' => 'decimal:4',
        'allow_fractional_sale' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Convert given quantity in this packaging unit to atomic base units.
     */
    public function toBaseUnits(float $quantity): int
    {
        return (int) round(bcmul((string) $quantity, (string) $this->conversion_factor, 4));
    }

    /**
     * Convert given base units to this packaging unit.
     */
    public function fromBaseUnits(int $baseUnits): float
    {
        if ($this->conversion_factor <= 0) {
            return 0.0;
        }

        return (float) bcdiv((string) $baseUnits, (string) $this->conversion_factor, 4);
    }
}
