<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Receipt;
use App\Models\Delivery;
use App\Models\Transfer;
use App\Models\StockLedger;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard with KPI metrics.
     */
    public function index(Request $request)
    {
        $locationId = $request->input('location_id');

        // Total distinct products
        $totalProducts = Product::count();

        // Low stock: products whose summed ledger quantity falls below their reorder_level.
        // Stock is NEVER on the Product model — we compute it from stock_ledger.
        $lowStockCount = DB::table('products')
            ->leftJoin('stock_ledger', function ($join) use ($locationId) {
                $join->on('products.id', '=', 'stock_ledger.product_id');
                if ($locationId) {
                    $join->where('stock_ledger.location_id', '=', $locationId);
                }
            })
            ->select('products.id', 'products.reorder_level', DB::raw('COALESCE(SUM(stock_ledger.quantity_change), 0) as total_stock'))
            ->groupBy('products.id', 'products.reorder_level')
            ->havingRaw('COALESCE(SUM(stock_ledger.quantity_change), 0) < products.reorder_level')
            ->get()->count();

        // Pending receipts (not yet Done or Canceled)
        $pendingReceipts = Receipt::whereNotIn('status', ['Done', 'Canceled'])->count();

        // Pending deliveries (not yet Done or Canceled)
        $pendingDeliveries = Delivery::whereNotIn('status', ['Done', 'Canceled'])->count();

        // Scheduled transfers (not yet Done or Canceled)
        $transfersQuery = Transfer::whereNotIn('status', ['Done', 'Canceled']);
        if ($locationId) {
            $transfersQuery->where(function ($q) use ($locationId) {
                $q->where('from_location_id', $locationId)
                  ->orWhere('to_location_id', $locationId);
            });
        }
        $scheduledTransfers = $transfersQuery->count();

        // Fetch locations for the dropdown
        $locations = Location::orderBy('name')->get();

        return view('dashboard.index', compact(
            'totalProducts',
            'lowStockCount',
            'pendingReceipts',
            'pendingDeliveries',
            'scheduledTransfers',
            'locations',
            'locationId'
        ));
    }
}
