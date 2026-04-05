<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Receipt;
use App\Models\Delivery;
use App\Models\Transfer;
use App\Models\Adjustment;
use App\Models\StockLedger;
use App\Models\Location;
use App\Models\PurchaseOrder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard with KPI metrics and chart data.
     */
    public function index(Request $request)
    {
        $locationId = $request->input('location_id');

        // ── KPI Cards ──────────────────────────────────────────

        $totalProducts = Product::count();

        $lowStockData = DB::table('products')
            ->leftJoin('stock_ledger', function ($join) use ($locationId) {
                $join->on('products.id', '=', 'stock_ledger.product_id');
                if ($locationId) {
                    $join->where('stock_ledger.location_id', '=', $locationId);
                }
            })
            ->select('products.id', 'products.reorder_level', DB::raw('COALESCE(SUM(stock_ledger.quantity_change), 0) as total_stock'))
            ->groupBy('products.id', 'products.reorder_level')
            ->havingRaw('COALESCE(SUM(stock_ledger.quantity_change), 0) < products.reorder_level')
            ->get();
            
        $lowStockCount = $lowStockData->count();

        // VALUATION: Total Warehouse Valuation (Stock * Unit Cost)
        $totalValuation = DB::table('products')
            ->join('stock_ledger', 'products.id', '=', 'stock_ledger.product_id')
            ->when($locationId, function($q) use ($locationId) {
                return $q->where('stock_ledger.location_id', $locationId);
            })
            ->select(DB::raw('SUM(stock_ledger.quantity_change * products.unit_cost) as total_value'))
            ->value('total_value') ?? 0;

        $pendingReceipts = Receipt::whereNotIn('status', ['Done', 'Canceled'])->count();
        
        // VALUATION: Pending PO Value
        $pendingPoValue = DB::table('purchase_orders')
            ->join('purchase_order_items', 'purchase_orders.id', '=', 'purchase_order_items.purchase_order_id')
            ->where('purchase_orders.status', 'Approved')
            ->select(DB::raw('SUM(purchase_order_items.quantity * purchase_order_items.unit_cost) as pending_value'))
            ->value('pending_value') ?? 0;

        $pendingDeliveries = Delivery::whereNotIn('status', ['Done', 'Canceled'])->count();

        $transfersQuery = Transfer::whereNotIn('status', ['Done', 'Canceled']);
        if ($locationId) {
            $transfersQuery->where(function ($q) use ($locationId) {
                $q->where('from_location_id', $locationId)
                  ->orWhere('to_location_id', $locationId);
            });
        }
        $scheduledTransfers = $transfersQuery->count();

        $locations = Location::orderBy('name')->get();

        // ── Chart 1: 30-Day Stock Movement Trend ───────────────
        //    Positive quantity_change = Stock IN, Negative = Stock OUT
        $startDate = Carbon::now()->subDays(29)->startOfDay();
        $endDate   = Carbon::now()->endOfDay();

        $dailyMovements = StockLedger::select(
                DB::raw('DATE(created_at) as day'),
                DB::raw('SUM(CASE WHEN quantity_change > 0 THEN quantity_change ELSE 0 END) as stock_in'),
                DB::raw('SUM(CASE WHEN quantity_change < 0 THEN ABS(quantity_change) ELSE 0 END) as stock_out')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        // Fill every day in the range (even days with zero movement)
        $trendLabels = [];
        $trendIn     = [];
        $trendOut    = [];
        foreach (CarbonPeriod::create($startDate, $endDate) as $date) {
            $key = $date->format('Y-m-d');
            $trendLabels[] = $date->format('M d');
            $trendIn[]     = (int) ($dailyMovements[$key]->stock_in ?? 0);
            $trendOut[]    = (int) ($dailyMovements[$key]->stock_out ?? 0);
        }

        // ── Chart 2: Product Category Distribution (By Valuation) ─────────────
        $categoryData = DB::table('products')
            ->join('stock_ledger', 'products.id', '=', 'stock_ledger.product_id')
            ->when($locationId, function($q) use ($locationId) {
                return $q->where('stock_ledger.location_id', $locationId);
            })
            ->select('products.category', DB::raw('SUM(stock_ledger.quantity_change * products.unit_cost) as category_value'))
            ->whereNotNull('products.category')
            ->groupBy('products.category')
            ->having('category_value', '>', 0)
            ->orderByDesc('category_value')
            ->get();

        $categoryLabels = $categoryData->pluck('category')->toArray();
        $categoryCounts = $categoryData->pluck('category_value')->map(fn($v) => round($v, 2))->toArray();

        // ── Chart 3: Low Stock Ranking (Top 8 closest to reorder) ──
        $stockRanking = DB::table('products')
            ->leftJoin('stock_ledger', function ($join) use ($locationId) {
                $join->on('products.id', '=', 'stock_ledger.product_id');
                if ($locationId) {
                    $join->where('stock_ledger.location_id', '=', $locationId);
                }
            })
            ->select(
                'products.name',
                'products.reorder_level',
                DB::raw('COALESCE(SUM(stock_ledger.quantity_change), 0) as current_stock')
            )
            ->groupBy('products.id', 'products.name', 'products.reorder_level')
            ->orderByRaw('COALESCE(SUM(stock_ledger.quantity_change), 0) - products.reorder_level ASC')
            ->limit(8)
            ->get();

        $stockLabels   = $stockRanking->pluck('name')->toArray();
        $stockCurrent  = $stockRanking->pluck('current_stock')->map(fn($v) => (int) $v)->toArray();
        $stockReorder  = $stockRanking->pluck('reorder_level')->map(fn($v) => (int) $v)->toArray();

        // ── Chart 4: Recent Activity Summary ───────────────────
        $recentActivity = [
            'receipts'    => Receipt::where('status', 'Done')->count(),
            'deliveries'  => Delivery::where('status', 'Done')->count(),
            'transfers'   => Transfer::where('status', 'Done')->count(),
            'adjustments' => Adjustment::where('status', 'Done')->count(),
        ];

        return view('dashboard.index', compact(
            'totalProducts',
            'totalValuation',
            'pendingPoValue',
            'lowStockCount',
            'pendingReceipts',
            'pendingDeliveries',
            'scheduledTransfers',
            'locations',
            'locationId',
            // Chart data
            'trendLabels', 'trendIn', 'trendOut',
            'categoryLabels', 'categoryCounts',
            'stockLabels', 'stockCurrent', 'stockReorder',
            'recentActivity'
        ));
    }
}
