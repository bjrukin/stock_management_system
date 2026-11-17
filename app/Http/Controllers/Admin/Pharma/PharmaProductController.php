<?php

namespace App\Http\Controllers\Admin\Pharma;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Category;
use App\Models\Manufacturer;
use App\Models\Product;
use App\Models\TaxCategory;
use App\Models\Unit;
use App\Support\PharmaAccess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmaProductController extends Controller
{
    public function index(Request $request)
    {
        $data['nav'] = 'pharma_catalog';
        $data['sub_nav'] = 'products';
        $data['page_title'] = 'Products & Items';

        $query = Product::with(['category', 'manufacturer', 'baseUnit', 'batches', 'inventoryBalances' => fn ($q) => PharmaAccess::scope($q)]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('code', 'like', "%{$s}%")
                    ->orWhere('generic_name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $data['products'] = $query->orderBy('name', 'asc')->paginate(20);
        $data['categories'] = Category::all();
        $data['manufacturers'] = Manufacturer::all();
        $data['units'] = Unit::all();

        return view('admin.pharma.products.index', $data);
    }

    public function create()
    {
        $data['nav'] = 'pharma_catalog';
        $data['sub_nav'] = 'products';
        $data['page_title'] = 'Add New Product';

        $data['categories'] = Category::all();
        $data['manufacturers'] = Manufacturer::all();
        $data['units'] = Unit::all();
        $data['taxCategories'] = TaxCategory::all();

        return view('admin.pharma.products.create', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:products,code',
            'brand' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'dosage_form' => 'nullable|string|max:50',
            'strength' => 'nullable|string|max:50',
            'base_unit_id' => 'required|exists:units,id',
            'tax_category_id' => 'nullable|exists:tax_categories,id',
            'mrp' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'max_level' => 'nullable|integer|min:0',
            'storage_requirement' => 'nullable|string|max:100',
            'dda_registration_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = 'PRD-'.strtoupper(substr(uniqid(), -6));
        }

        $validated['mrp'] = $validated['mrp'] ?? 0.00;
        $validated['purchase_price'] = $validated['purchase_price'] ?? 0.00;
        $validated['selling_price'] = $validated['selling_price'] ?? 0.00;
        $validated['wholesale_price'] = $validated['wholesale_price'] ?? 0.00;
        $validated['narcotic_psychotropic'] = $request->boolean('narcotic_psychotropic');
        $validated['prescription_required'] = $request->boolean('prescription_required');
        $validated['allow_fractional_base'] = $request->boolean('allow_fractional_base');
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        $product = Product::create($validated);

        return redirect()->route('admin.pharma.products')
            ->with('success', "Product '{$product->name}' ({$product->code}) created successfully.");
    }

    public function edit(int $id)
    {
        $product = Product::findOrFail($id);

        $data['nav'] = 'pharma_catalog';
        $data['sub_nav'] = 'products';
        $data['page_title'] = "Edit Product: {$product->name}";

        $data['product'] = $product;
        $data['categories'] = Category::all();
        $data['manufacturers'] = Manufacturer::all();
        $data['units'] = Unit::all();
        $data['taxCategories'] = TaxCategory::all();

        return view('admin.pharma.products.edit', $data);
    }

    public function update(Request $request, int $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'generic_name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:products,code,'.$product->id,
            'brand' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'manufacturer_id' => 'nullable|exists:manufacturers,id',
            'dosage_form' => 'nullable|string|max:50',
            'strength' => 'nullable|string|max:50',
            'base_unit_id' => 'required|exists:units,id',
            'tax_category_id' => 'nullable|exists:tax_categories,id',
            'mrp' => 'nullable|numeric|min:0',
            'purchase_price' => 'nullable|numeric|min:0',
            'selling_price' => 'nullable|numeric|min:0',
            'wholesale_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'max_level' => 'nullable|integer|min:0',
            'storage_requirement' => 'nullable|string|max:100',
            'dda_registration_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $validated['mrp'] = $validated['mrp'] ?? $product->mrp ?? 0.00;
        $validated['purchase_price'] = $validated['purchase_price'] ?? $product->purchase_price ?? 0.00;
        $validated['selling_price'] = $validated['selling_price'] ?? $product->selling_price ?? 0.00;
        $validated['wholesale_price'] = $validated['wholesale_price'] ?? $product->wholesale_price ?? 0.00;
        $validated['narcotic_psychotropic'] = $request->boolean('narcotic_psychotropic');
        $validated['prescription_required'] = $request->boolean('prescription_required');
        $validated['allow_fractional_base'] = $request->boolean('allow_fractional_base');
        $validated['is_active'] = $request->boolean('is_active');

        if ((int) $validated['base_unit_id'] !== (int) $product->base_unit_id && $product->hasTransactionHistory()) {
            return back()->withInput()->with('error', 'The base unit cannot be changed after stock or transaction history exists.');
        }

        $product->update($validated);

        return redirect()->route('admin.pharma.products')
            ->with('success', "Product '{$product->name}' updated successfully.");
    }

    public function destroy(int $id)
    {
        $product = Product::findOrFail($id);
        $name = $product->name;
        $deleted = DB::transaction(function () use ($product) {
            $product = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            if ($product->hasTransactionHistory()) {
                return false;
            }
            $product->delete();

            return true;
        });
        if (! $deleted) {
            return back()->with('error', 'This product has stock or transaction history. Use Edit to deactivate it instead.');
        }

        return redirect()->route('admin.pharma.products')
            ->with('success', "Product '{$name}' deleted successfully.");
    }

    public function batches(Request $request)
    {
        $data['nav'] = 'pharma_catalog';
        $data['sub_nav'] = 'batches';
        $data['page_title'] = 'Batch Register';

        $query = Batch::with(['product', 'inventoryBalances' => fn ($q) => PharmaAccess::scope($q)]);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('batch_number', 'like', "%{$s}%")
                    ->orWhereRelation('product', 'name', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'quarantined') {
                $query->where('is_quarantined', true);
            } elseif ($status === 'recalled') {
                $query->where('is_recalled', true);
            } elseif ($status === 'expired') {
                $query->where('expiry_date_ad', '<=', Carbon::today());
            } elseif ($status === 'active') {
                $query->where('is_quarantined', false)
                    ->where('is_recalled', false)
                    ->where('expiry_date_ad', '>', Carbon::today());
            }
        }

        $data['batches'] = $query->orderBy('expiry_date_ad', 'asc')->paginate(25);

        return view('admin.pharma.products.batches', $data);
    }
}
