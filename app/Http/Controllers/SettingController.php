<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Location;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with('locations')->orderBy('name')->get();

        return view('settings.index', compact('warehouses'));
    }

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
}
