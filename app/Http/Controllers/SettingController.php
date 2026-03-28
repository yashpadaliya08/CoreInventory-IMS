<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Location;
use App\Models\CompanySetting;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with('locations')->orderBy('name')->get();
        $categories = ProductCategory::orderBy('name')->get();

        // Load company profile settings
        $company = [
            'company_name' => CompanySetting::getValue('company_name', ''),
            'tax_id'       => CompanySetting::getValue('tax_id', ''),
            'address'      => CompanySetting::getValue('address', ''),
            'phone'        => CompanySetting::getValue('phone', ''),
            'email'        => CompanySetting::getValue('email', ''),
            'logo_path'    => CompanySetting::getValue('logo_path', ''),
        ];

        return view('settings.index', compact('warehouses', 'categories', 'company'));
    }

    // ── Company Profile ─────────────────────────────────────

    public function updateCompany(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'nullable|string|max:255',
            'tax_id'       => 'nullable|string|max:100',
            'address'      => 'nullable|string|max:500',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
        ]);

        foreach ($validated as $key => $value) {
            CompanySetting::setValue($key, $value);
        }

        // Handle logo upload separately
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('company', 'public');
            CompanySetting::setValue('logo_path', $path);
        }

        return redirect()->route('settings.index')->with('success', 'Company profile updated.');
    }

    // ── Warehouses ───────────────────────────────────────────

    public function storeWarehouse(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code',
            'location_address' => 'nullable|string',
        ]);

        Warehouse::create($validated);

        return redirect()->route('settings.index')->with('success', 'New Warehouse created.');
    }

    public function storeLocation(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'name'         => 'required|string|max:255',
            'type'         => 'required|in:internal,vendor,customer,inventory_loss',
        ]);

        Location::create($validated);

        return redirect()->route('settings.index')->with('success', 'New Location created.');
    }

    public function editWarehouse(Warehouse $warehouse)
    {
        return view('settings.warehouse_edit', compact('warehouse'));
    }

    public function updateWarehouse(Request $request, Warehouse $warehouse)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:warehouses,code,'.$warehouse->id,
            'location_address' => 'nullable|string',
        ]);

        $warehouse->update($validated);
        return redirect()->route('settings.index')->with('success', 'Warehouse updated successfully.');
    }

    public function destroyWarehouse(Warehouse $warehouse)
    {
        $warehouse->delete();
        return redirect()->route('settings.index')->with('success', 'Warehouse deleted successfully.');
    }

    public function destroyLocation(Location $location)
    {
        $location->delete();
        return redirect()->route('settings.index')->with('success', 'Location deleted successfully.');
    }

    // ── Product Categories ──────────────────────────────────

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:product_categories,name',
            'description' => 'nullable|string|max:500',
        ]);

        ProductCategory::create($validated);

        return redirect()->route('settings.index')->with('success', 'Product category created.');
    }

    public function destroyCategory(ProductCategory $category)
    {
        $category->delete();
        return redirect()->route('settings.index')->with('success', 'Product category deleted.');
    }
}

