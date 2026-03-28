<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\ReceiptItem;
use App\Models\Product;
use App\Models\Location;
use App\Models\StockLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
    /**
     * Display a listing of receipts with dynamic query filters.
     * Supports: ?status=, ?document_type=, ?sku=
     */
    public function index(Request $request)
    {
        $receipts = Receipt::query()
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('vendor_name'), function ($q) use ($request) {
                $q->where('vendor_name', 'like', '%' . $request->vendor_name . '%');
            })
            ->when($request->filled('sku'), function ($q) use ($request) {
                $q->whereHas('receiptItems.product', function ($sub) use ($request) {
                    $sub->where('sku', $request->sku);
                });
            })
            ->with('receiptItems.product')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('receipts.index', compact('receipts'));
    }

    /**
     * Show the form for creating a new receipt.
     */
    public function create()
    {
        $products  = Product::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('receipts.create', compact('products', 'locations'));
    }

    /**
     * Store a newly created receipt with its items.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_name'       => ['nullable', 'string', 'max:255'],
            'expected_date'     => ['nullable', 'date'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.product_id'=> ['required', 'exists:products,id'],
            'items.*.quantity'  => ['required', 'integer', 'min:1'],
        ]);

        return DB::transaction(function () use ($validated) {
            $receipt = Receipt::create([
                'reference_no'     => 'RCP-' . strtoupper(uniqid()),
                'vendor_name'      => $validated['vendor_name'] ?? null,
                'expected_date'    => $validated['expected_date'] ?? null,
                'status'           => 'Draft',
            ]);

            foreach ($validated['items'] as $item) {
                ReceiptItem::create([
                    'receipt_id' => $receipt->id,
                    'product_id' => $item['product_id'],
                    'quantity'   => $item['quantity'],
                ]);
            }

            return redirect()->route('receipts.show', $receipt)
                ->with('success', 'Receipt created successfully.');
        });
    }

    /**
     * Display the specified receipt.
     */
    public function show(Receipt $receipt)
    {
        $receipt->load('receiptItems.product');

        return view('receipts.show', compact('receipt'));
    }

    /**
     * Show the form for editing the specified receipt.
     */
    public function edit(Receipt $receipt)
    {
        $receipt->load('receiptItems');
        $products  = Product::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();

        return view('receipts.edit', compact('receipt', 'products', 'locations'));
    }

    /**
     * Update the specified receipt.
     */
    public function update(Request $request, Receipt $receipt)
    {
        if ($receipt->status === 'Done') {
            return back()->withErrors(['status' => 'Cannot edit a validated receipt.']);
        }

        $validated = $request->validate([
            'vendor_name'   => ['nullable', 'string', 'max:255'],
            'expected_date' => ['nullable', 'date'],
        ]);

        $receipt->update($validated);

        return redirect()->route('receipts.show', $receipt)
            ->with('success', 'Receipt updated successfully.');
    }

    /**
     * Remove the specified receipt.
     */
    public function destroy(Receipt $receipt)
    {
        if ($receipt->status === 'Done') {
            return back()->withErrors(['status' => 'Cannot delete a validated receipt.']);
        }

        $receipt->receiptItems()->delete();
        $receipt->delete();

        return redirect()->route('receipts.index')
            ->with('success', 'Receipt deleted successfully.');
    }

    /**
     * Validate a receipt: write positive stock ledger entries and mark as Done.
     *
     * CRITICAL: Each receipt item creates a +quantity ledger row at the receipt's location.
     */
    public function validateReceipt(Receipt $receipt)
    {
        if ($receipt->status === 'Done') {
            return back()->withErrors(['status' => 'This receipt has already been validated.']);
        }

        $receipt->load('receiptItems');

        return DB::transaction(function () use ($receipt) {
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

            foreach ($receipt->receiptItems as $item) {
                StockLedger::create([
                    'product_id'      => $item->product_id,
                    'location_id'     => $location->id,
                    'reference_type'  => Receipt::class,
                    'reference_id'    => $receipt->id,
                    'quantity_change' => $item->quantity, // POSITIVE entry
                ]);
            }

            $receipt->update([
                'status'       => 'Done',
            ]);

            return redirect()->route('receipts.show', $receipt)
                ->with('success', 'Receipt validated. Stock ledger updated.');
        });
    }
}
