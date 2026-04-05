<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display the global activity audit log.
     * Supports: ?search=, ?log_name=, ?event=
     */
    public function index(Request $request)
    {
        $activities = Activity::query()
            ->with('causer', 'subject')
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->search . '%')
                  ->orWhere('properties', 'like', '%' . $request->search . '%');
            })
            ->when($request->filled('log_name'), function ($q) use ($request) {
                $q->where('log_name', $request->log_name);
            })
            ->when($request->filled('event'), function ($q) use ($request) {
                $q->where('event', $request->event);
            })
            ->orderByDesc('created_at')
            ->paginate(50)
            ->withQueryString();

        // Get unique log names for the filter dropdown
        $logNames = Activity::distinct()->pluck('log_name')->sort();

        return view('activity-log.index', compact('activities', 'logNames'));
    }
}
