<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors with search filters.
     */
    public function index(Request $request)
    {
        $vendors = Vendor::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $request->search . '%');
            })
            ->withCount('purchaseOrders')
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        return view('vendors.index', compact('vendors'));
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        return view('vendors.create');
    }

    /**
     * Store a newly created vendor.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['nullable', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:50'],
            'address'        => ['nullable', 'string', 'max:1000'],
            'contact_person' => ['nullable', 'string', 'max:255'],
        ]);

        $vendor = Vendor::create($validated);

        return redirect()->route('vendors.show', $vendor)
            ->with('success', 'Vendor created successfully.');
    }

    /**
     * Display the specified vendor with PO history.
     */
    public function show(Vendor $vendor)
    {
        $vendor->load(['purchaseOrders' => function ($q) {
            $q->with('items.product')->orderByDesc('id');
        }]);

        return view('vendors.show', compact('vendor'));
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    /**
     * Update the specified vendor.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['nullable', 'email', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:50'],
            'address'        => ['nullable', 'string', 'max:1000'],
            'contact_person' => ['nullable', 'string', 'max:255'],
        ]);

        $vendor->update($validated);

        return redirect()->route('vendors.show', $vendor)
            ->with('success', 'Vendor updated successfully.');
    }

    /**
     * Remove the specified vendor (soft delete).
     */
    public function destroy(Vendor $vendor)
    {
        // Prevent deletion if there are approved POs
        if ($vendor->purchaseOrders()->where('status', 'Approved')->exists()) {
            return back()->withErrors(['vendor' => 'Cannot delete a vendor with approved purchase orders.']);
        }

        $vendor->delete();

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }
}
