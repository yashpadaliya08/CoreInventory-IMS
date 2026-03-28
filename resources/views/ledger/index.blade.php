@extends('layouts.app')

@push('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 24px;
    }
    .filter-panel {
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(16px);
        border: 1px solid rgba(255,255,255,0.8);
        border-radius: var(--radius-lg);
        padding: 20px;
        margin-bottom: 24px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .badge-loc { background: rgba(99, 102, 241, 0.1); color: var(--primary); border: 1px solid rgba(99,102,241,0.2); }
    .badge-ref { background: rgba(0, 0, 0, 0.04); color: var(--text-muted); border: 1px solid rgba(0,0,0,0.05); }
    .qty-change { font-family: 'Outfit'; font-size: 1.15rem; font-weight: 700; background: rgba(255,255,255,0.7); padding: 4px 12px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .qty-pos { color: #16a34a; }
    .qty-neg { color: #dc2626; }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Move History Ledger</h2>
            <p class="text-muted m-0 mt-1" style="font-size: 1rem;">Immutable audit trail of all automated stock movements.</p>
        </div>
    </div>

    <div class="filter-panel">
        <form method="GET" action="{{ route('ledger.index') }}" class="row g-3 align-items-end">
            <div class="col-md-9">
                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Search Ledger</label>
                <div class="position-relative">
                    <i data-feather="search" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                    <input type="text" name="search" class="form-control" style="padding-left: 42px; height: 44px; background: rgba(255,255,255,0.9);" placeholder="Search by Product Name, SKU, or Location..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100 d-flex justify-content-center align-items-center gap-2" style="height: 44px;">
                    <i data-feather="filter" style="width: 16px;"></i> Retrieve Data
                </button>
            </div>
        </form>
    </div>

    <div class="glass-panel overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead style="background: rgba(0,0,0,0.02);">
                    <tr>
                        <th class="ps-4 border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Execution Time</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Product Identity</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Associated Location</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Quantity Delta</th>
                        <th class="border-0 text-center" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Source Record</th>
                    </tr>
                </thead>
                <tbody style="border-top: 2px solid rgba(0,0,0,0.05);">
                    @forelse($ledgers as $ledger)
                    <tr>
                        <td class="ps-4 text-muted" style="font-family: 'Outfit'; font-size: 0.95rem; font-weight: 500;">
                            {{ \Carbon\Carbon::parse($ledger->created_at)->format('d M Y, H:i') }}
                        </td>
                        <td>
                            <div class="fw-semibold" style="color: var(--text-main); font-size: 1.05rem;">{{ $ledger->product->name ?? 'Unknown Product' }}</div>
                            <div class="text-muted" style="font-size: 0.75rem; font-family: 'Outfit';">SKU: {{ $ledger->product->sku ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="status-badge badge-loc">
                                <i data-feather="map-pin" style="width: 12px; margin-right: 2px;"></i> 
                                {{ $ledger->location->name ?? 'Unknown Location' }}
                            </span>
                        </td>
                        <td>
                            @if($ledger->quantity_change > 0)
                                <span class="qty-change qty-pos">+{{ $ledger->quantity_change }}</span>
                            @elseif($ledger->quantity_change < 0)
                                <span class="qty-change qty-neg">{{ $ledger->quantity_change }}</span>
                            @else
                                <span class="qty-change text-muted">{{ $ledger->quantity_change }}</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="status-badge badge-ref">
                                <i data-feather="file-text" style="width: 12px; margin-right: 2px;"></i>
                                {{ class_basename($ledger->reference_type) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <i data-feather="database" style="width: 48px; height: 48px; color: var(--text-muted); opacity: 0.5; margin-bottom: 16px;"></i>
                            <h5 style="font-family: 'Outfit'; font-weight: 600; color: var(--text-main);">No movements recorded</h5>
                            <p class="text-muted" style="max-width: 300px; margin: 0 auto;">Stock movements will appear here automatically once logistics operations are validated.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4" style="opacity: 0.9;">
        @if(method_exists($ledgers, 'links'))
            {{ $ledgers->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
@endsection
