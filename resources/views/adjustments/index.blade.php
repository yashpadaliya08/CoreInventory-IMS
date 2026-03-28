@extends('layouts.app')

@push('styles')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; }
    .filter-panel { background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.8); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.70rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 4px; }
    .badge-Draft { background: rgba(100, 116, 139, 0.1); color: #475569; border: 1px solid rgba(100,116,139,0.2); }
    .badge-Done { background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34,197,94,0.2); }
    .ref-link { color: var(--text-main); font-weight: 700; font-family: 'Outfit'; text-decoration: none; transition: color 0.2s; }
    .ref-link:hover { color: var(--primary); }
    .loc-tag { font-family: 'Outfit'; font-weight: 600; padding: 4px 8px; background: rgba(0,0,0,0.04); border-radius: 6px; font-size: 0.9rem;}
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Inventory Adjustments</h2>
            <p class="text-muted m-0 mt-1" style="font-size: 1rem;">Correct anomalies, register damage, and manage shrinkage.</p>
        </div>
        @if(auth()->user() && auth()->user()->isManagerOrAbove())
        <a href="{{ route('adjustments.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="height: 44px; padding: 0 24px; background: linear-gradient(135deg, #1e293b, #334155) !important;">
            <i data-feather="sliders" style="width: 18px;"></i> New Adjustment
        </a>
        @endif
    </div>

    <div class="filter-panel">
        <form method="GET" action="{{ route('adjustments.index') }}" class="row g-3 align-items-end">
            <div class="col-md-7">
                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Search (SKU)</label>
                <div class="position-relative">
                    <i data-feather="search" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                    <input type="text" name="sku" class="form-control" style="padding-left: 42px; height: 44px; background: rgba(255,255,255,0.9);" placeholder="Product SKU..." value="{{ request('sku') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Document Status</label>
                <select name="status" class="form-select" style="height: 44px; background: rgba(255,255,255,0.9);">
                    <option value="">All Statuses</option>
                    <option value="Draft" {{ request('status') == 'Draft' ? 'selected' : '' }}>Draft</option>
                    <option value="Done" {{ request('status') == 'Done' ? 'selected' : '' }}>Done</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100 d-flex justify-content-center align-items-center gap-2" style="height: 44px; background: rgba(0,0,0,0.05); color: var(--text-main); border: 1px solid rgba(0,0,0,0.1); font-weight: 600;">
                    <i data-feather="filter" style="width: 16px;"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <div class="glass-panel overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead style="background: rgba(0,0,0,0.02);">
                    <tr>
                        <th class="ps-4 border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Reference</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Target Location</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Target Product</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Status</th>
                        <th class="border-0 text-end pe-4"></th>
                    </tr>
                </thead>
                <tbody style="border-top: 2px solid rgba(0,0,0,0.05);">
                    @forelse($adjustments as $adjustment)
                    <tr>
                        <td class="ps-4"><a href="{{ route('adjustments.show', $adjustment->id) }}" class="ref-link">{{ $adjustment->reference_no }}</a></td>
                        <td><span class="loc-tag"><i data-feather="map-pin" style="width: 12px; margin-right:4px;"></i>{{ $adjustment->location->name ?? 'N/A' }}</span></td>
                        <td>
                            <div class="fw-semibold" style="color: var(--text-main);">{{ $adjustment->product->name ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="status-badge badge-{{ $adjustment->status }}">{{ $adjustment->status }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('adjustments.show', $adjustment->id) }}" class="btn btn-sm btn-light" style="border: 1px solid rgba(0,0,0,0.1); font-weight: 600;">Open</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i data-feather="sliders" style="width: 48px; height: 48px; color: var(--text-muted); opacity: 0.5; margin-bottom: 16px;"></i>
                            <h5 style="font-family: 'Outfit'; font-weight: 600; color: var(--text-main);">No Adjustments Found</h5>
                            <p class="text-muted" style="max-width: 300px; margin: 0 auto;">No records found matching your filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
