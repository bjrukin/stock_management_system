<?php

namespace App\Http\Controllers\Admin\Pharma;

use App\Http\Controllers\Controller;
use App\Support\PharmaAccess;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class PharmaMasterController extends Controller
{
    public function warehouses(Request $request)
    {
        $data['nav'] = 'pharma_catalog';
        $data['sub_nav'] = 'warehouses';
        $data['page_title'] = 'Warehouses';

        $query = PharmaAccess::scope(Warehouse::query(), ['id'])->withCount('locations');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('address', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $data['warehouses'] = $query->orderByDesc('is_primary')->orderBy('name')->paginate(15);

        return view('admin.pharma.masters.warehouses', $data);
    }

    public function storeWarehouse(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        $validated['is_primary'] = $request->boolean('is_primary');
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        if ($validated['is_primary']) {
            PharmaAccess::scope(Warehouse::query(), ['id'])->where('is_primary', true)->update(['is_primary' => false]);
        }

        $warehouse = Warehouse::create($validated);

        return redirect()->route('admin.pharma.warehouses')
            ->with('success', "Warehouse '{$warehouse->name}' created successfully.");
    }

    public function updateWarehouse(Request $request, int $id)
    {
        $warehouse = PharmaAccess::scope(Warehouse::query(), ['id'])->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code,' . $warehouse->id,
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string|max:255',
        ]);

        $validated['is_primary'] = $request->boolean('is_primary');
        $validated['is_active'] = $request->boolean('is_active');

        if ($validated['is_primary']) {
            PharmaAccess::scope(Warehouse::query(), ['id'])->where('id', '!=', $warehouse->id)->where('is_primary', true)->update(['is_primary' => false]);
        }

        $warehouse->update($validated);

        return redirect()->route('admin.pharma.warehouses')
            ->with('success', "Warehouse '{$warehouse->name}' updated successfully.");
    }

    public function destroyWarehouse(int $id)
    {
        $warehouse = PharmaAccess::scope(Warehouse::query(), ['id'])->findOrFail($id);
        $name = $warehouse->name;
        $warehouse->delete();

        return redirect()->route('admin.pharma.warehouses')
            ->with('success', "Warehouse '{$name}' deleted successfully.");
    }

    public function warehouseLocations(Request $request, int $id)
    {
        $warehouse = PharmaAccess::scope(Warehouse::query(), ['id'])->withCount('locations')->findOrFail($id);

        $data['nav'] = 'pharma_catalog';
        $data['sub_nav'] = 'warehouses';
        $data['page_title'] = "Locations - {$warehouse->name}";
        $data['warehouse'] = $warehouse;
        $data['warehouses'] = PharmaAccess::scope(Warehouse::query(), ['id'])->where('is_active', true)->orderBy('name')->get();

        $query = PharmaAccess::scope(\App\Models\WarehouseLocation::query())->with('warehouse')->where('warehouse_id', $warehouse->id);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('storage_condition', 'like', "%{$s}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $data['locations'] = $query->orderBy('name')->paginate(20);

        return view('admin.pharma.masters.warehouse_locations', $data);
    }

    public function allLocations(Request $request)
    {
        $data['nav'] = 'pharma_catalog';
        $data['sub_nav'] = 'warehouses';
        $data['page_title'] = 'All Warehouse Locations';
        $data['warehouse'] = null;
        $data['warehouses'] = PharmaAccess::scope(Warehouse::query(), ['id'])->where('is_active', true)->orderBy('name')->get();

        $query = PharmaAccess::scope(\App\Models\WarehouseLocation::query())->with('warehouse');

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
            $data['warehouse'] = PharmaAccess::scope(Warehouse::query(), ['id'])->find($request->warehouse_id);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%")
                  ->orWhere('storage_condition', 'like', "%{$s}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $data['locations'] = $query->orderBy('warehouse_id')->orderBy('name')->paginate(25);

        return view('admin.pharma.masters.warehouse_locations', $data);
    }

    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50',
            'type' => 'nullable|in:storage,receiving,dispatch,quarantine,damaged,expired_disposal,returns,cold_storage',
            'storage_condition' => 'nullable|string|max:100',
            'capacity' => 'nullable|numeric|min:0',
        ]);

        $exists = PharmaAccess::scope(\App\Models\WarehouseLocation::query())->where('warehouse_id', $validated['warehouse_id'])
            ->where('code', $validated['code'])
            ->exists();
        if ($exists) {
            return back()->withInput()->with('error', "Location code '{$validated['code']}' already exists in this warehouse.");
        }

        if (empty($validated['type'])) {
            $validated['type'] = 'storage';
        }

        $validated['is_sellable'] = $request->has('is_sellable') ? $request->boolean('is_sellable') : true;
        $validated['is_available'] = $request->has('is_available') ? $request->boolean('is_available') : true;
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        $location = \App\Models\WarehouseLocation::create($validated);

        if ($request->header('referer')) {
            return redirect()->back()->with('success', "Location '{$location->name}' added successfully.");
        }

        return redirect()->route('admin.pharma.warehouses')
            ->with('success', "Location '{$location->name}' added to warehouse successfully.");
    }

    public function updateLocation(Request $request, int $id)
    {
        $location = PharmaAccess::scope(\App\Models\WarehouseLocation::query())->findOrFail($id);

        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50',
            'type' => 'nullable|in:storage,receiving,dispatch,quarantine,damaged,expired_disposal,returns,cold_storage',
            'storage_condition' => 'nullable|string|max:100',
            'capacity' => 'nullable|numeric|min:0',
        ]);

        $exists = PharmaAccess::scope(\App\Models\WarehouseLocation::query())->where('warehouse_id', $validated['warehouse_id'])
            ->where('code', $validated['code'])
            ->where('id', '!=', $location->id)
            ->exists();
        if ($exists) {
            return back()->withInput()->with('error', "Location code '{$validated['code']}' already exists in this warehouse.");
        }

        if (empty($validated['type'])) {
            $validated['type'] = 'storage';
        }

        $validated['is_sellable'] = $request->boolean('is_sellable');
        $validated['is_available'] = $request->boolean('is_available');
        $validated['is_active'] = $request->boolean('is_active');

        $location->update($validated);

        return back()->with('success', "Location '{$location->name}' ({$location->code}) updated successfully.");
    }

    public function destroyLocation(int $id)
    {
        $location = PharmaAccess::scope(\App\Models\WarehouseLocation::query())->findOrFail($id);
        $name = $location->name;
        $location->delete();

        return back()->with('success', "Location '{$name}' deleted successfully.");
    }

    public function suppliers(Request $request)
    {
        $data['nav'] = 'pharma_catalog';
        $data['sub_nav'] = 'suppliers';
        $data['page_title'] = 'Suppliers';

        $query = Supplier::withCount(['purchaseOrders', 'goodsReceipts']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('pan_vat_number', 'like', "%{$s}%")
                  ->orWhere('code', 'like', "%{$s}%");
            });
        }

        $data['suppliers'] = $query->orderBy('name', 'asc')->paginate(20);

        return view('admin.pharma.masters.suppliers', $data);
    }

    public function createSupplier()
    {
        $data['nav'] = 'pharma_catalog';
        $data['sub_nav'] = 'suppliers';
        $data['page_title'] = 'Add New Supplier';

        return view('admin.pharma.masters.supplier_create', $data);
    }

    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:suppliers,code',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'pan_vat_number' => 'nullable|string|max:50',
            'dda_license_number' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'credit_period_days' => 'nullable|integer|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = 'SUP-' . strtoupper(substr(uniqid(), -6));
        }

        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;
        if (!isset($validated['current_balance'])) {
            $validated['current_balance'] = $validated['opening_balance'] ?? 0.00;
        }

        $supplier = Supplier::create($validated);

        return redirect()->route('admin.pharma.suppliers')
            ->with('success', "Supplier '{$supplier->name}' ({$supplier->code}) created successfully.");
    }

    public function editSupplier(int $id)
    {
        $supplier = Supplier::findOrFail($id);

        $data['nav'] = 'pharma_catalog';
        $data['sub_nav'] = 'suppliers';
        $data['page_title'] = "Edit Supplier: {$supplier->name}";
        $data['supplier'] = $supplier;

        return view('admin.pharma.masters.supplier_edit', $data);
    }

    public function updateSupplier(Request $request, int $id)
    {
        $supplier = Supplier::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:suppliers,code,' . $supplier->id,
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'pan_vat_number' => 'nullable|string|max:50',
            'dda_license_number' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'credit_period_days' => 'nullable|integer|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $supplier->update($validated);

        return redirect()->route('admin.pharma.suppliers')
            ->with('success', "Supplier '{$supplier->name}' updated successfully.");
    }

    public function destroySupplier(int $id)
    {
        $supplier = Supplier::findOrFail($id);
        $name = $supplier->name;
        $supplier->delete();

        return redirect()->route('admin.pharma.suppliers')
            ->with('success', "Supplier '{$name}' deleted successfully.");
    }

    public function customers(Request $request)
    {
        $data['nav'] = 'pharma_catalog';
        $data['sub_nav'] = 'customers';
        $data['page_title'] = 'Customers (Pharmacies / Clinics)';

        $query = Customer::withCount(['invoices']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('pan_vat_number', 'like', "%{$s}%")
                  ->orWhere('dda_license_number', 'like', "%{$s}%");
            });
        }

        $data['customers'] = $query->orderBy('name', 'asc')->paginate(20);

        return view('admin.pharma.masters.customers', $data);
    }

    public function createCustomer()
    {
        $data['nav'] = 'pharma_catalog';
        $data['sub_nav'] = 'customers';
        $data['page_title'] = 'Add New Customer';

        return view('admin.pharma.masters.customer_create', $data);
    }

    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:customers,code',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'pan_vat_number' => 'nullable|string|max:50',
            'dda_license_number' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'credit_period_days' => 'nullable|integer|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
            'opening_balance' => 'nullable|numeric|min:0',
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = 'CUST-' . strtoupper(substr(uniqid(), -6));
        }

        $validated['credit_stop'] = $request->boolean('credit_stop');
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;
        if (!isset($validated['current_balance'])) {
            $validated['current_balance'] = $validated['opening_balance'] ?? 0.00;
        }

        $customer = Customer::create($validated);

        return redirect()->route('admin.pharma.customers')
            ->with('success', "Customer '{$customer->name}' ({$customer->code}) created successfully.");
    }

    public function editCustomer(int $id)
    {
        $customer = Customer::findOrFail($id);

        $data['nav'] = 'pharma_catalog';
        $data['sub_nav'] = 'customers';
        $data['page_title'] = "Edit Customer: {$customer->name}";
        $data['customer'] = $customer;

        return view('admin.pharma.masters.customer_edit', $data);
    }

    public function updateCustomer(Request $request, int $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:customers,code,' . $customer->id,
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'pan_vat_number' => 'nullable|string|max:50',
            'dda_license_number' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'credit_period_days' => 'nullable|integer|min:0',
            'credit_limit' => 'nullable|numeric|min:0',
        ]);

        $validated['credit_stop'] = $request->boolean('credit_stop');
        $validated['is_active'] = $request->boolean('is_active');

        $customer->update($validated);

        return redirect()->route('admin.pharma.customers')
            ->with('success', "Customer '{$customer->name}' updated successfully.");
    }

    public function destroyCustomer(int $id)
    {
        $customer = Customer::findOrFail($id);
        $name = $customer->name;
        $customer->delete();

        return redirect()->route('admin.pharma.customers')
            ->with('success', "Customer '{$name}' deleted successfully.");
    }
}
