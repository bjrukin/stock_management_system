<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use Auditable, HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'generic_name',
        'brand',
        'category_id',
        'manufacturer_id',
        'dosage_form',
        'strength',
        'base_unit_id',
        'tax_category_id',
        'mrp',
        'purchase_price',
        'selling_price',
        'wholesale_price',
        'min_stock',
        'reorder_level',
        'max_level',
        'storage_requirement',
        'narcotic_psychotropic',
        'prescription_required',
        'dda_registration_number',
        'allow_fractional_base',
        'image_path',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'mrp' => 'decimal:4',
        'purchase_price' => 'decimal:4',
        'selling_price' => 'decimal:4',
        'wholesale_price' => 'decimal:4',
        'min_stock' => 'integer',
        'reorder_level' => 'integer',
        'max_level' => 'integer',
        'narcotic_psychotropic' => 'boolean',
        'prescription_required' => 'boolean',
        'allow_fractional_base' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function hasTransactionHistory(): bool
    {
        if ($this->batches()->exists() || $this->inventoryBalances()->exists() || $this->stockMovements()->exists()) {
            return true;
        }
        foreach ([PurchaseOrderLine::class, GoodsReceiptLine::class, PurchaseReturnLine::class,
            SaleInvoiceLine::class, SaleReturnLine::class, StockTransferLine::class, StockAdjustmentLine::class] as $model) {
            if ($model::where('product_id', $this->id)->exists()) {
                return true;
            }
        }

        return false;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id');
    }

    public function taxCategory()
    {
        return $this->belongsTo(TaxCategory::class);
    }

    public function packagingLevels()
    {
        return $this->hasMany(ProductPackagingLevel::class)->orderBy('level_order');
    }

    public function barcodes()
    {
        return $this->hasMany(ProductBarcode::class);
    }

    public function batches()
    {
        return $this->hasMany(Batch::class);
    }

    public function inventoryBalances()
    {
        return $this->hasMany(InventoryBalance::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Get aggregate available stock across all active/sellable locations.
     */
    public function getAvailableStockAttribute(): int
    {
        return (int) $this->inventoryBalances()
            ->whereHas('location', fn ($q) => $q->where('is_sellable', true)->where('is_available', true))
            ->whereHas('batch', fn ($q) => $q->where('is_quarantined', false)->where('is_recalled', false)->where('expiry_date_ad', '>', now()->toDateString()))
            ->selectRaw('COALESCE(SUM(quantity_on_hand - quantity_reserved - quantity_damaged - quantity_quarantined - quantity_in_transit_out), 0) as total')
            ->value('total');
    }

    /**
     * Get aggregate physical on-hand stock.
     */
    public function getOnHandStockAttribute(): int
    {
        return (int) $this->inventoryBalances()->sum('quantity_on_hand');
    }
}
