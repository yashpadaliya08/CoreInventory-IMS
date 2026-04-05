<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Vendor;
use App\Models\Receipt;
use App\Models\ReceiptItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of purchase orders with filters.
     */
    public function index(Request $request)
    {
        $purchaseOrders = PurchaseOrder::query()
            ->when($request->filled('status'), function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->filled('vendor'), function ($q) use ($request) {
                $q->where('vendor_id', $request->vendor);
            })
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('reference_no', 'like', '%' . $request->search . '%');
            })
            ->with('vendor', 'items')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $vendors = Vendor::orderBy('name')->get();

        return view('purchase_orders.index', compact('purchaseOrders', 'vendors'));
    }

    /**
     * Show the form for creating a new purchase order.
     */
    public function create()
    {
        $vendors  = Vendor::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchase_orders.create', compact('vendors', 'products'));
    }

    /**
     * Store a newly created purchase order with its line items.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id'          => ['required', 'exists:vendors,id'],
            'expected_date'      => ['nullable', 'date'],
            'notes'              => ['nullable', 'string', 'max:2000'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.unit_cost'  => ['required', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($validated) {
            $po = PurchaseOrder::create([
                'reference_no'  => 'PO-' . strtoupper(uniqid()),
                'vendor_id'     => $validated['vendor_id'],
                'expected_date' => $validated['expected_date'] ?? null,
                'notes'         => $validated['notes'] ?? null,
                'status'        => 'Draft',
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id'        => $item['product_id'],
                    'quantity'          => $item['quantity'],
                    'unit_cost'         => $item['unit_cost'],
                ]);
            }

            return redirect()->route('purchase-orders.show', $po)
                ->with('success', 'Purchase Order created successfully.');
        });
    }

    /**
     * Display the specified purchase order.
     */
    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('vendor', 'items.product', 'receipt');

        return view('purchase_orders.show', compact('purchaseOrder'));
    }

    /**
     * Show the form for editing the specified purchase order.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (in_array($purchaseOrder->status, ['Approved', 'Cancelled'])) {
            return back()->withErrors(['status' => 'Cannot edit an approved or cancelled purchase order.']);
        }

        $purchaseOrder->load('items');
        $vendors  = Vendor::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('purchase_orders.edit', compact('purchaseOrder', 'vendors', 'products'));
    }

    /**
     * Update the specified purchase order.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (in_array($purchaseOrder->status, ['Approved', 'Cancelled'])) {
            return back()->withErrors(['status' => 'Cannot edit an approved or cancelled purchase order.']);
        }

        $validated = $request->validate([
            'vendor_id'          => ['required', 'exists:vendors,id'],
            'expected_date'      => ['nullable', 'date'],
            'notes'              => ['nullable', 'string', 'max:2000'],
            'items'              => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity'   => ['required', 'integer', 'min:1'],
            'items.*.unit_cost'  => ['required', 'numeric', 'min:0'],
        ]);

        return DB::transaction(function () use ($validated, $purchaseOrder) {
            $purchaseOrder->update([
                'vendor_id'     => $validated['vendor_id'],
                'expected_date' => $validated['expected_date'] ?? null,
                'notes'         => $validated['notes'] ?? null,
            ]);

            // Replace all items
            $purchaseOrder->items()->delete();

            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id'        => $item['product_id'],
                    'quantity'          => $item['quantity'],
                    'unit_cost'         => $item['unit_cost'],
                ]);
            }

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order updated successfully.');
        });
    }

    /**
     * Approve a PO: auto-generate a Receipt draft with all PO items pre-filled.
     *
     * CRITICAL FLOW:
     * 1. Creates Receipt (Draft) linked to PO's vendor
     * 2. Copies all PO line items into ReceiptItems
     * 3. Updates PO status to 'Approved' and links the generated receipt_id
     * 4. Optionally updates product unit_cost from the PO line items
     */
    public function approve(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Draft' && $purchaseOrder->status !== 'Sent') {
            return back()->withErrors(['status' => 'Only Draft or Sent purchase orders can be approved.']);
        }

        $purchaseOrder->load('items.product', 'vendor');

        return DB::transaction(function () use ($purchaseOrder) {
            // 1. Create a Receipt draft linked to the vendor
            $receipt = Receipt::create([
                'reference_no'  => 'RCP-' . strtoupper(uniqid()),
                'vendor_name'   => $purchaseOrder->vendor->name,
                'vendor_id'     => $purchaseOrder->vendor_id,
                'status'        => 'Draft',
                'expected_date' => $purchaseOrder->expected_date,
            ]);

            // 2. Copy all PO items into receipt items
            foreach ($purchaseOrder->items as $poItem) {
                ReceiptItem::create([
                    'receipt_id' => $receipt->id,
                    'product_id' => $poItem->product_id,
                    'quantity'   => $poItem->quantity,
                ]);

                // 3. Update product unit_cost if the PO specifies one
                if ($poItem->unit_cost > 0) {
                    $poItem->product->update([
                        'unit_cost' => $poItem->unit_cost,
                    ]);
                }
            }

            // 4. Mark PO as Approved and link the receipt
            $purchaseOrder->update([
                'status'     => 'Approved',
                'receipt_id' => $receipt->id,
            ]);

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order approved! Receipt ' . $receipt->reference_no . ' has been auto-generated.');
        });
    }

    /**
     * Cancel a purchase order.
     */
    public function cancel(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'Approved') {
            return back()->withErrors(['status' => 'Cannot cancel an already approved purchase order.']);
        }

        $purchaseOrder->update(['status' => 'Cancelled']);

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Purchase Order has been cancelled.');
    }

    /**
     * Remove the specified purchase order (soft delete).
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status === 'Approved') {
            return back()->withErrors(['status' => 'Cannot delete an approved purchase order.']);
        }

        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();

        return redirect()->route('purchase-orders.index')
            ->with('success', 'Purchase Order deleted successfully.');
    }
}
