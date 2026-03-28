<?php

namespace App\Http\Controllers;

use App\Models\StockLedger;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    /**
     * Display stock ledger history with dynamic filters.
     * Supports: ?location=, ?category=, ?product_id=, ?reference_type=, ?date_from=, ?date_to=
     */
    public function index(Request $request)
    {
        $ledgers = StockLedger::query()
            ->with(['product', 'location'])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->whereHas('product', function ($sub) use ($request) {
                    $sub->where('name', 'like', '%' . $request->search . '%');
                })->orWhereHas('location', function ($sub) use ($request) {
                    $sub->where('name', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->filled('location'), function ($q) use ($request) {
                $q->where('location_id', $request->location);
            })
            ->when($request->filled('category'), function ($q) use ($request) {
                $q->whereHas('product', function ($sub) use ($request) {
                    $sub->where('category', $request->category);
                });
            })
            ->when($request->filled('product_id'), function ($q) use ($request) {
                $q->where('product_id', $request->product_id);
            })
            ->when($request->filled('reference_type'), function ($q) use ($request) {
                $q->where('reference_type', $request->reference_type);
            })
            ->when($request->filled('date_from'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->date_to);
            })
            ->orderByDesc('created_at')
            ->paginate(50)
            ->withQueryString();

        return view('ledger.index', compact('ledgers'));
    }
}
