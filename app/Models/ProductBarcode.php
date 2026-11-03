<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBarcode extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'barcode',
        'type',
        'packaging_level_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function packagingLevel()
    {
        return $this->belongsTo(ProductPackagingLevel::class, 'packaging_level_id');
    }
}
