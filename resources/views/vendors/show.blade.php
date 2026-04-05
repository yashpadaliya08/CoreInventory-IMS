@extends('layouts.app')

@push('styles')
<style>
    .page-header { margin-bottom: 32px; }
    .glass-card { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.8); border-radius: var(--radius-lg); padding: 32px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 24px; }
    .section-title { font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.25rem; display: flex; align-items: center; gap: 8px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid rgba(0,0,0,0.03); }
    .info-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; }
    .info-value { font-size: 1.05rem; font-weight: 600; color: var(--text-main); border: 1px solid rgba(0,0,0,0.05); padding: 12px 16px; border-radius: 8px; background: rgba(255,255,255,0.7); box-shadow: inset 0 2px 4px rgba(0,0,0,0.01); }
    .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 0.70rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 4px; }
    .badge-Draft { background: rgba(100, 116, 139, 0.1); color: #475569; border: 1px solid rgba(100,116,139,0.2); }
    .badge-Sent { background: rgba(56, 189, 248, 0.1); color: #0284c7; border: 1px solid rgba(56,189,248,0.2); }
    .badge-Approved { background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34,197,94,0.2); }
    .badge-Cancelled { background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239,68,68,0.2); }
    .ref-link { color: var(--text-main); font-weight: 700; font-family: 'Outfit'; text-decoration: none; transition: color 0.2s; }
    .ref-link:hover { color: var(--primary); }
    .stat-card { background: rgba(255,255,255,0.5); border: 1px solid rgba(0,0,0,0.05); border-radius: 12px; padding: 20px; text-align: center; }
    .stat-value { font-family: 'Outfit'; font-weight: 800; font-size: 2rem; color: var(--text-main); letter-spacing: -1px; }
    .stat-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; margin-top: 4px; }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <a href="{{ route('vendors.index') }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;"><i data-feather="arrow-left" style="width: 14px;"></i> Back to Vendors</a>
            <h2 class="m-0 mt-2" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">{{ $vendor->name }}</h2>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if(auth()->user() && auth()->user()->isManagerOrAbove())
            <a href="{{ route('vendors.edit', $vendor) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 fw-bold" style="height: 48px; border-radius: 10px;">
                <i data-feather="edit-2" style="width: 16px;"></i> Edit
            </a>
            @endif
            <a href="{{ route('purchase-orders.create') }}?vendor_id={{ $vendor->id }}" class="btn btn-primary d-flex align-items-center gap-2 fw-bold" style="height: 48px; border-radius: 10px;">
                <i data-feather="file-plus" style="width: 16px;"></i> New PO
            </a>
            @if(auth()->user() && auth()->user()->isAdmin())
            <form action="{{ route('vendors.destroy', $vendor) }}" method="POST" onsubmit="return confirm('Delete this vendor?');" class="m-0">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger d-flex align-items-center gap-2 fw-bold" style="height: 48px; border-radius: 10px;">
                    <i data-feather="trash-2" style="width: 16px;"></i> Delete
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="glass-card h-100">
                <h6 class="section-title"><i data-feather="briefcase" style="color: var(--primary);"></i> Contact Details</h6>
                
                <div class="mb-4">
                    <div class="info-label">Contact Person</div>
                    <div class="info-value d-flex align-items-center gap-2">
                        <i data-feather="user" style="width: 16px; color: var(--text-muted);"></i>
                        {{ $vendor->contact_person ?? 'Unspecified' }}
                    </div>
                </div>

                <div class="mb-4">
                    <div class="info-label">Email Address</div>
                    <div class="info-value d-flex align-items-center gap-2">
                        <i data-feather="mail" style="width: 16px; color: var(--text-muted);"></i>
                        {{ $vendor->email ?? 'Unspecified' }}
                    </div>
                </div>

                <div class="mb-4">
                    <div class="info-label">Phone Number</div>
                    <div class="info-value d-flex align-items-center gap-2">
                        <i data-feather="phone" style="width: 16px; color: var(--text-muted);"></i>
                        {{ $vendor->phone ?? 'Unspecified' }}
                    </div>
                </div>

                <div class="mb-2">
                    <div class="info-label">Business Address</div>
                    <div class="info-value d-flex align-items-center gap-2">
                        <i data-feather="map-pin" style="width: 16px; color: var(--text-muted); flex-shrink: 0;"></i>
                        {{ $vendor->address ?? 'Unspecified' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Stats Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-value">{{ $vendor->purchaseOrders->count() }}</div>
                        <div class="stat-label">Total POs</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-value" style="color: #16a34a;">{{ $vendor->purchaseOrders->where('status', 'Approved')->count() }}</div>
                        <div class="stat-label">Approved</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card">
                        <div class="stat-value" style="color: var(--primary);">₹{{ number_format($vendor->total_purchase_value, 2) }}</div>
                        <div class="stat-label">Total Value</div>
                    </div>
                </div>
            </div>

            <!-- PO History -->
            <div class="glass-panel overflow-hidden">
                <div class="p-4 border-bottom" style="background: rgba(255,255,255,0.4);">
                    <h6 class="m-0" style="font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
                        <i data-feather="file-text" style="color: var(--secondary); width: 20px;"></i> Purchase Order History
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: rgba(0,0,0,0.02);">
                            <tr>
                                <th class="ps-4 border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">REFERENCE</th>
                                <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">EXPECTED DATE</th>
                                <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">STATUS</th>
                                <th class="border-0 text-end pe-4" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">TOTAL COST</th>
                            </tr>
                        </thead>
                        <tbody style="border-top: 2px solid rgba(0,0,0,0.05);">
                            @forelse($vendor->purchaseOrders as $po)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('purchase-orders.show', $po->id) }}" class="ref-link">{{ $po->reference_no }}</a>
                                </td>
                                <td style="font-family: 'Outfit'; font-weight: 500; color: var(--text-muted);">
                                    {{ $po->expected_date ? $po->expected_date->format('M d, Y') : 'N/A' }}
                                </td>
                                <td>
                                    <span class="status-badge badge-{{ $po->status }}">{{ $po->status }}</span>
                                </td>
                                <td class="text-end pe-4" style="font-family: 'Outfit'; font-weight: 700; color: var(--text-main);">
                                    ₹{{ number_format($po->total_cost, 2) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <p class="text-muted mb-0">No purchase orders yet for this vendor.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
