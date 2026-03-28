<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use App\Models\DeliveryItem;
use App\Models\Product;
use App\Models\Location;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DeliveryController extends Controller
{
    /**
     * Display a listing of deliveries with dynamic query filters.
     * Supports: ?status=, ?document_type=, ?sku=
     */
    public function index(Request $request)
    {
        $deliveries = Delivery::query()
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('customer_name'), function ($q) use ($request) {
                $q->where('customer_name', 'like', '%' . $request->customer_name . '%');
            })
            ->when($request->filled('sku'), function ($q) use ($request) {
                $q->whereHas('deliveryItems.product', function ($sub) use ($request) {
                    $sub->where('sku', $request->sku);
                });
            })
            ->with('deliveryItems.product')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('deliveries.index', compact('deliveries'));
    }

    /**
     * Show the form for creating a new delivery.
     */
    public function create()
    {
        $products  = Product::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('deliveries.create', compact('products', 'locations'));
    }

    /**
     * Store a newly created delivery with its items.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'    => ['nullable', 'string', 'max:255'],
            'scheduled_date'   => ['nullable', 'date'],
            'items'            => ['required', 'array', 'min:1'],
            'items.*.product_id'=> ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($validated) {
            $delivery = Delivery::create([
                'reference_no'     => 'DEL-' . strtoupper(uniqid()),
                'customer_name'    => $validated['customer_name'] ?? null,
                'scheduled_date'   => $validated['scheduled_date'] ?? null,
                'status'           => 'Draft',
            ]);

            foreach ($validated['items'] as $item) {
                DeliveryItem::create([
                    'delivery_id' => $delivery->id,
                    'product_id'  => $item['product_id'],
                    'quantity'    => $item['quantity'],
                ]);
            }

            return redirect()->route('deliveries.show', $delivery)
                ->with('success', 'Delivery created successfully.');
        });
    }

    /**
     * Display the specified delivery.
     */
    public function show(Delivery $delivery)
    {
        $delivery->load('deliveryItems.product');

        return view('deliveries.show', compact('delivery'));
    }

    /**
     * Show the form for editing the specified delivery.
     */
    public function edit(Delivery $delivery)
    {
        $delivery->load('deliveryItems');
        $products  = Product::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('deliveries.edit', compact('delivery', 'products', 'locations'));
    }

    /**
     * Update the specified delivery.
     */
    public function update(Request $request, Delivery $delivery)
    {
        if ($delivery->status === 'Done') {
            return back()->withErrors(['status' => 'Cannot edit a validated delivery.']);
        }

        $validated = $request->validate([
            'customer_name'    => ['nullable', 'string', 'max:255'],
            'scheduled_date'   => ['nullable', 'date'],
        ]);

        $delivery->update($validated);

        return redirect()->route('deliveries.show', $delivery)
            ->with('success', 'Delivery updated successfully.');
    }

    /**
     * Remove the specified delivery.
     */
    public function destroy(Delivery $delivery)
    {
        if ($delivery->status === 'Done') {
            return back()->withErrors(['status' => 'Cannot delete a validated delivery.']);
        }

        $delivery->deliveryItems()->delete();
        $delivery->delete();

        return redirect()->route('deliveries.index')
            ->with('success', 'Delivery deleted successfully.');
    }

    /**
     * Validate a delivery: write negative stock ledger entries and mark as Done.
     *
     * CRITICAL: Each delivery item creates a -quantity ledger row at the delivery's location.
     * Insufficient stock check is enforced before committing.
     */
    public function validateDelivery(Delivery $delivery)
    {
        if ($delivery->status === 'Done') {
            return back()->withErrors(['status' => 'This delivery has already been validated.']);
        }

        $delivery->load('deliveryItems');

        return DB::transaction(function () use ($delivery) {
            // Retrieve default Location
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

            // Check stock sufficiency before creating ledger entries
            foreach ($delivery->deliveryItems as $item) {
                $currentStock = StockLedger::where('product_id', $item->product_id)
                    ->where('location_id', $location->id)
                    ->sum('quantity_change');

                if ($currentStock < $item->quantity) {
                    $product = $item->product;
                    throw new \Exception(
                        "Insufficient stock for product [{$product->sku}] at this location. " .
                        "Available: {$currentStock}, Required: {$item->quantity}."
                    );
                }
            }

            foreach ($delivery->deliveryItems as $item) {
                StockLedger::create([
                    'product_id'      => $item->product_id,
                    'location_id'     => $location->id,
                    'reference_type'  => Delivery::class,
                    'reference_id'    => $delivery->id,
                    'quantity_change' => -$item->quantity, // NEGATIVE entry
                ]);
            }

            $delivery->update([
                'status'       => 'Done',
            ]);

            return redirect()->route('deliveries.show', $delivery)
                ->with('success', 'Delivery validated. Stock ledger updated.');
        });
    }
}
