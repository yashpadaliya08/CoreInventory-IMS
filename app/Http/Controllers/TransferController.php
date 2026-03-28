<?php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\Product;
use App\Models\Location;
use App\Models\Warehouse;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    /**
     * Display a listing of transfers.
     */
    public function index(Request $request)
    {
        $transfers = Transfer::query()
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->with(['fromLocation', 'toLocation', 'transferItems.product'])
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('transfers.index', compact('transfers'));
    }

    /**
     * Show the form for creating a new transfer.
     */
    public function create()
    {
        $products   = Product::orderBy('name')->get();
        $locations  = Location::orderBy('name')->get();
        $warehouses = Warehouse::orderBy('name')->get();

        return view('transfers.create', compact('products', 'locations', 'warehouses'));
    }

    /**
     * Store a newly created transfer with its items.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_location_id'     => ['required', 'exists:locations,id'],
            'to_location_id'       => ['required', 'exists:locations,id', 'different:from_location_id'],
            'scheduled_date'       => ['nullable', 'date'],
            'notes'                => ['nullable', 'string'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.product_id'   => ['required', 'exists:products,id'],
            'items.*.quantity'     => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($validated) {
            $transfer = Transfer::create([
                'reference_no'     => 'TRF-' . strtoupper(uniqid()),
                'from_location_id' => $validated['from_location_id'],
                'to_location_id'   => $validated['to_location_id'],
                'status'           => 'Draft',
            ]);

            foreach ($validated['items'] as $item) {
                TransferItem::create([
                    'transfer_id' => $transfer->id,
                    'product_id'  => $item['product_id'],
                    'quantity'    => $item['quantity'],
                ]);
            }

            return redirect()->route('transfers.show', $transfer)
                ->with('success', 'Transfer scheduled successfully.');
        });
    }

    /**
     * Display the specified transfer.
     */
    public function show(Transfer $transfer)
    {
        $transfer->load('fromLocation', 'toLocation', 'transferItems.product');

        return view('transfers.show', compact('transfer'));
    }

    /**
     * Show the form for editing the specified transfer.
     */
    public function edit(Transfer $transfer)
    {
        $transfer->load('transferItems');
        $products  = Product::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('transfers.edit', compact('transfer', 'products', 'locations'));
    }

    /**
     * Update the specified transfer.
     */
    public function update(Request $request, Transfer $transfer)
    {
        if ($transfer->status === 'Done') {
            return back()->withErrors(['status' => 'Cannot edit a validated transfer.']);
        }

        $validated = $request->validate([
            'from_location_id' => ['required', 'exists:locations,id'],
            'to_location_id'   => ['required', 'exists:locations,id', 'different:from_location_id'],
        ]);

        $transfer->update($validated);

        return redirect()->route('transfers.show', $transfer)
            ->with('success', 'Transfer updated successfully.');
    }

    /**
     * Remove the specified transfer.
     */
    public function destroy(Transfer $transfer)
    {
        if ($transfer->status === 'Done') {
            return back()->withErrors(['status' => 'Cannot delete a validated transfer.']);
        }

        $transfer->transferItems()->delete();
        $transfer->delete();

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer deleted successfully.');
    }

    /**
     * Validate a transfer: creates TWO stock ledger entries per item.
     *
     * CRITICAL:
     *   - One NEGATIVE entry at from_location (stock leaves).
     *   - One POSITIVE entry at to_location (stock arrives).
     * Stock sufficiency is checked at from_location before committing.
     */
    public function validateTransfer(Transfer $transfer)
    {
        if ($transfer->status === 'Done') {
            return back()->withErrors(['status' => 'This transfer has already been validated.']);
        }

        $transfer->load('transferItems');

        return DB::transaction(function () use ($transfer) {
            // Check stock sufficiency at source location
            foreach ($transfer->transferItems as $item) {
                $currentStock = StockLedger::where('product_id', $item->product_id)
                    ->where('location_id', $transfer->from_location_id)
                    ->sum('quantity_change');

                if ($currentStock < $item->quantity) {
                    $product = $item->product;
                    throw new \Exception(
                        "Insufficient stock for product [{$product->sku}] at source location. " .
                        "Available: {$currentStock}, Required: {$item->quantity}."
                    );
                }
            }

            foreach ($transfer->transferItems as $item) {
                // NEGATIVE entry: stock leaves from_location
                StockLedger::create([
                    'product_id'      => $item->product_id,
                    'location_id'     => $transfer->from_location_id,
                    'reference_type'  => Transfer::class,
                    'reference_id'    => $transfer->id,
                    'quantity_change' => -$item->quantity,
                ]);

                // POSITIVE entry: stock arrives at to_location
                StockLedger::create([
                    'product_id'      => $item->product_id,
                    'location_id'     => $transfer->to_location_id,
                    'reference_type'  => Transfer::class,
                    'reference_id'    => $transfer->id,
                    'quantity_change' => $item->quantity,
                ]);
            }

            $transfer->update([
                'status'       => 'Done',
            ]);

            return redirect()->route('transfers.show', $transfer)
                ->with('success', 'Transfer validated. Stock moved in ledger.');
        });
    }
}
