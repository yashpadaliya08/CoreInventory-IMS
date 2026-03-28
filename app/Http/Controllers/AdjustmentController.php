<?php

namespace App\Http\Controllers;

use App\Models\Adjustment;
use App\Models\Product;
use App\Models\Location;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdjustmentController extends Controller
{
    /**
     * Display a listing of adjustments.
     */
    public function index(Request $request)
    {
        $adjustments = Adjustment::query()
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('product_id'), function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            })
            ->with(['product', 'location'])
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('adjustments.index', compact('adjustments'));
    }

    /**
     * Show the form for creating a new adjustment.
     */
    public function create()
    {
        $products  = Product::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('adjustments.create', compact('products', 'locations'));
    }

    /**
     * Store a newly created adjustment.
     *
     * The difference_quantity is calculated server-side:
     *   difference = physical_count - recorded_stock
     * where recorded_stock is the SUM of stock_ledger for that product+location.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id'     => ['required', 'exists:products,id'],
            'location_id'    => ['required', 'exists:locations,id'],
            'physical_count' => ['required', 'integer', 'min:0'],
            'reason'         => ['nullable', 'string', 'max:500'],
        ]);

        // Calculate recorded stock from ledger (the source of truth)
        $recordedStock = StockLedger::where('product_id', $validated['product_id'])
            ->where('location_id', $validated['location_id'])
            ->sum('quantity_change');

        $differenceQuantity = $validated['physical_count'] - $recordedStock;

        $adjustment = Adjustment::create([
            'reference_no'        => 'ADJ-' . strtoupper(uniqid()),
            'product_id'          => $validated['product_id'],
            'location_id'         => $validated['location_id'],
            'physical_quantity'   => $validated['physical_count'],
            'recorded_quantity'   => $recordedStock,
            'difference_quantity' => $differenceQuantity,
            'status'              => 'Draft',
        ]);

        return redirect()->route('adjustments.show', $adjustment)
            ->with('success', "Adjustment created. Difference: {$differenceQuantity}.");
    }

    /**
     * Display the specified adjustment.
     */
    public function show(Adjustment $adjustment)
    {
        $adjustment->load('product', 'location');

        return view('adjustments.show', compact('adjustment'));
    }

    /**
     * Show the form for editing the specified adjustment.
     */
    public function edit(Adjustment $adjustment)
    {
        if ($adjustment->status === 'Done') {
            return back()->withErrors(['status' => 'Cannot edit a validated adjustment.']);
        }

        $products  = Product::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('adjustments.edit', compact('adjustment', 'products', 'locations'));
    }

    /**
     * Update the specified adjustment (recalculates difference).
     */
    public function update(Request $request, Adjustment $adjustment)
    {
        if ($adjustment->status === 'Done') {
            return back()->withErrors(['status' => 'Cannot edit a validated adjustment.']);
        }

        $validated = $request->validate([
            'product_id'     => ['required', 'exists:products,id'],
            'location_id'    => ['required', 'exists:locations,id'],
            'physical_count' => ['required', 'integer', 'min:0'],
            'reason'         => ['nullable', 'string', 'max:500'],
        ]);

        $recordedStock = StockLedger::where('product_id', $validated['product_id'])
            ->where('location_id', $validated['location_id'])
            ->sum('quantity_change');

        $differenceQuantity = $validated['physical_count'] - $recordedStock;

        $adjustment->update([
            'product_id'          => $validated['product_id'],
            'location_id'         => $validated['location_id'],
            'physical_quantity'   => $validated['physical_count'],
            'recorded_quantity'   => $recordedStock,
            'difference_quantity' => $differenceQuantity,
        ]);

        return redirect()->route('adjustments.show', $adjustment)
            ->with('success', "Adjustment updated. Difference: {$differenceQuantity}.");
    }

    /**
     * Remove the specified adjustment.
     */
    public function destroy(Adjustment $adjustment)
    {
        if ($adjustment->status === 'Done') {
            return back()->withErrors(['status' => 'Cannot delete a validated adjustment.']);
        }

        $adjustment->delete();

        return redirect()->route('adjustments.index')
            ->with('success', 'Adjustment deleted successfully.');
    }

    /**
     * Validate an adjustment: log the difference_quantity to the stock ledger.
     *
     * CRITICAL: The ledger entry quantity = difference_quantity.
     *   - Positive if physical_count > recorded_stock (gain).
     *   - Negative if physical_count < recorded_stock (loss).
     *   - Zero differences are not logged.
     */
    public function validateAdjustment(Adjustment $adjustment)
    {
        if ($adjustment->status === 'Done') {
            return back()->withErrors(['status' => 'This adjustment has already been validated.']);
        }

        if ($adjustment->difference_quantity == 0) {
            $adjustment->update([
                'status'      => 'Done',
            ]);

            return redirect()->route('adjustments.show', $adjustment)
                ->with('info', 'No stock difference. Adjustment marked as Done with no ledger entry.');
        }

        return DB::transaction(function () use ($adjustment) {
            StockLedger::create([
                'product_id'      => $adjustment->product_id,
                'location_id'     => $adjustment->location_id,
                'reference_type'  => Adjustment::class,
                'reference_id'    => $adjustment->id,
                'quantity_change' => $adjustment->difference_quantity, // + or - based on physical vs recorded
            ]);

            $adjustment->update([
                'status'      => 'Done',
            ]);

            return redirect()->route('adjustments.show', $adjustment)
                ->with('success', 'Adjustment validated. Ledger entry created.');
        });
    }
}
