<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Adjustment;
use App\Models\StockLedger;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $products = Product::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'ilike', '%' . $request->search . '%')
                  ->orWhere('sku', 'ilike', '%' . $request->search . '%');
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->where('category', $request->category);
            })
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $categories = Product::select('category')->whereNotNull('category')->distinct()->pluck('category');

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $locations = Location::all();

        return view('products.create', compact('locations'));
    }

    /**
     * Store a newly created product.
     *
     * CRITICAL: If initial_stock is provided, an Adjustment + StockLedger entry
     * is created immediately. Stock is NEVER stored on the Product model.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'sku'             => ['required', 'string', 'max:100', 'unique:products,sku'],
            'category'        => ['nullable', 'string', 'max:100'],
            'unit_of_measure' => ['required', 'string', 'max:50'],
            'reorder_level'   => ['required', 'integer', 'min:0'],
            'initial_stock'   => ['nullable', 'integer', 'min:0'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $product = Product::create([
                'name'            => $validated['name'],
                'sku'             => $validated['sku'],
                'category'        => $validated['category'] ?? null,
                'unit_of_measure' => $validated['unit_of_measure'],
                'reorder_level'   => $validated['reorder_level'],
            ]);

            // If initial stock is provided, create an Adjustment + Ledger entry
            if (!empty($validated['initial_stock']) && $validated['initial_stock'] > 0) {
                // Find or create a default location if no location was passed since the form lacks it
                $location = Location::firstOrCreate(
                    ['name' => 'Main Warehouse'],
                    [
                        'warehouse_id' => \App\Models\Warehouse::firstOrCreate(
                            ['name' => 'Main Facility'],
                            ['code' => 'MAIN']
                        )->id,
                        'type' => 'internal'
                    ]
                );

                $adjustment = \App\Models\Adjustment::create([
                    'reference_no'        => 'INI-' . strtoupper(uniqid()),
                    'product_id'          => $product->id,
                    'location_id'         => $location->id,
                    'recorded_quantity'   => 0,
                    'physical_quantity'   => $validated['initial_stock'],
                    'difference_quantity' => $validated['initial_stock'],
                    'status'              => 'Done',
                ]);

                \App\Models\StockLedger::create([
                    'product_id'      => $product->id,
                    'location_id'     => $location->id,
                    'reference_type'  => \App\Models\Adjustment::class,
                    'reference_id'    => $adjustment->id,
                    'quantity_change' => $validated['initial_stock'], // positive entry
                ]);
            }

            return redirect()->route('products.show', $product)
                ->with('success', 'Product created successfully.');
        });
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product)
    {
        // Compute current stock per location from ledger
        $stockByLocation = StockLedger::where('product_id', $product->id)
            ->selectRaw('location_id, SUM(quantity_change) as total_stock')
            ->groupBy('location_id')
            ->with('location')
            ->get();

        return view('products.show', compact('product', 'stockByLocation'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'            => ['required', 'string', 'max:255'],
            'sku'             => ['required', 'string', 'max:100', 'unique:products,sku,' . $product->id],
            'category'        => ['nullable', 'string', 'max:100'],
            'unit_of_measure' => ['required', 'string', 'max:50'],
            'reorder_level'   => ['required', 'integer', 'min:0']
        ]);

        $product->update($validated);

        return redirect()->route('products.show', $product)
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
