<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;

class AlertController extends Controller
{
    /**
     * Display the Low Stock Alerts page.
     */
    public function index()
    {
        $lowStockProducts = DB::table('products')
            ->leftJoin('stock_ledger', 'products.id', '=', 'stock_ledger.product_id')
            ->leftJoin('vendors', 'products.id', '=', DB::raw('(SELECT product_id FROM purchase_order_items JOIN purchase_orders ON purchase_orders.id = purchase_order_items.purchase_order_id WHERE purchase_orders.status = "Approved" LIMIT 1)')) // A rough join to get latest vendor, better done in code
            ->select(
                'products.id',
                'products.sku',
                'products.name',
                'products.reorder_level',
                'products.unit_cost',
                DB::raw('COALESCE(SUM(stock_ledger.quantity_change), 0) as current_stock')
            )
            ->groupBy('products.id', 'products.sku', 'products.name', 'products.reorder_level', 'products.unit_cost')
            ->havingRaw('COALESCE(SUM(stock_ledger.quantity_change), 0) < products.reorder_level')
            ->orderByRaw('current_stock - products.reorder_level ASC') // Order by urgency
            ->get();

        // Let's get the most recent vendor for each product to allow quick reordering
        $productIds = $lowStockProducts->pluck('id')->toArray();
        
        $recentVendors = DB::table('purchase_order_items')
            ->join('purchase_orders', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
            ->join('vendors', 'vendors.id', '=', 'purchase_orders.vendor_id')
            ->whereIn('purchase_order_items.product_id', $productIds)
            ->where('purchase_orders.status', 'Approved')
            ->select('purchase_order_items.product_id', 'vendors.id as vendor_id', 'vendors.name as vendor_name')
            ->orderBy('purchase_orders.created_at', 'desc')
            ->get()
            ->groupBy('product_id');

        foreach ($lowStockProducts as $item) {
            $item->suggested_vendor = $recentVendors->has($item->id) ? $recentVendors[$item->id]->first() : null;
            $item->deficit = $item->reorder_level - $item->current_stock;
        }

        return view('alerts.index', compact('lowStockProducts'));
    }

    /**
     * Quick Reorder: Auto-create a Draft PO for a specific product and vendor.
     */
    public function quickReorder(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'vendor_id'  => 'required|exists:vendors,id',
            'quantity'   => 'required|numeric|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        $po = PurchaseOrder::create([
            'reference_no' => 'PO-' . date('Y-') . str_pad(PurchaseOrder::max('id') + 1, 4, '0', STR_PAD_LEFT),
            'vendor_id'    => $request->vendor_id,
            'status'       => 'Draft',
            'expected_date'=> now()->addDays(7),
            'notes'        => 'Auto-generated via Quick Reorder Alert for Low Stock tracking.',
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'product_id'        => $product->id,
            'quantity'          => $request->quantity,
            'unit_cost'         => $product->unit_cost,
        ]);

        return redirect()->route('purchase-orders.show', $po->id)
                         ->with('success', 'Draft Purchase Order created successfully!');
    }
}
