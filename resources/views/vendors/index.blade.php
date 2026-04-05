@extends('layouts.app')

@push('styles')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; }
    .filter-panel { background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.8); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .ref-link { color: var(--text-main); font-weight: 700; font-family: 'Outfit'; text-decoration: none; transition: color 0.2s; }
    .ref-link:hover { color: var(--primary); }
    .vendor-meta { font-size: 0.85rem; color: var(--text-muted); display: flex; align-items: center; gap: 6px; }
    .po-count-badge { background: rgba(99,102,241,0.1); color: var(--primary); font-family: 'Outfit'; font-weight: 700; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Vendors</h2>
            <p class="text-muted m-0 mt-1" style="font-size: 1rem;">Manage your suppliers and procurement partners.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('export.vendors') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="height: 44px; padding: 0 20px; font-weight: 600; border-radius: 10px;">
                <i data-feather="download" style="width: 16px;"></i> Excel
            </a>
            <a href="{{ route('export.vendors.csv') }}" class="btn btn-outline-secondary d-flex align-items-center gap-2" style="height: 44px; padding: 0 20px; font-weight: 600; border-radius: 10px;">
                <i data-feather="file-text" style="width: 16px;"></i> CSV
            </a>
            @if(auth()->user() && auth()->user()->isManagerOrAbove())
            <a href="{{ route('vendors.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="height: 44px; padding: 0 24px;">
                <i data-feather="plus" style="width: 18px;"></i> Add Vendor
            </a>
            @endif
        </div>
    </div>

    <div class="filter-panel">
        <form method="GET" action="{{ route('vendors.index') }}" class="row g-3 align-items-end">
            <div class="col-md-8">
                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Search</label>
                <div class="position-relative">
                    <i data-feather="search" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                    <input type="text" name="search" class="form-control" style="padding-left: 42px; height: 44px; background: rgba(255,255,255,0.9);" placeholder="Search by name, email, or contact person..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100 d-flex justify-content-center align-items-center gap-2" style="height: 44px; background: rgba(0,0,0,0.05); color: var(--text-main); border: 1px solid rgba(0,0,0,0.1); font-weight: 600;">
                    <i data-feather="filter" style="width: 16px;"></i> Filter
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('vendors.index') }}" class="btn btn-outline-secondary w-100 d-flex justify-content-center align-items-center gap-2" style="height: 44px; font-weight: 600;">
                    <i data-feather="x" style="width: 16px;"></i> Clear
                </a>
            </div>
        </form>
    </div>

    <div class="glass-panel overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead style="background: rgba(0,0,0,0.02);">
                    <tr>
                        <th class="ps-4 border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">VENDOR NAME</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">CONTACT PERSON</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">EMAIL</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">PHONE</th>
                        <th class="border-0 text-center" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">POs</th>
                        <th class="border-0 text-end pe-4"></th>
                    </tr>
                </thead>
                <tbody style="border-top: 2px solid rgba(0,0,0,0.05);">
                    @forelse($vendors as $vendor)
                    <tr>
                        <td class="ps-4">
                            <a href="{{ route('vendors.show', $vendor->id) }}" class="ref-link">{{ $vendor->name }}</a>
                        </td>
                        <td>
                            <div class="fw-semibold" style="color: var(--text-main);">{{ $vendor->contact_person ?? '—' }}</div>
                        </td>
                        <td>
                            <span class="vendor-meta">
                                @if($vendor->email)
                                    <i data-feather="mail" style="width: 14px;"></i> {{ $vendor->email }}
                                @else
                                    —
                                @endif
                            </span>
                        </td>
                        <td>
                            <span class="vendor-meta">
                                @if($vendor->phone)
                                    <i data-feather="phone" style="width: 14px;"></i> {{ $vendor->phone }}
                                @else
                                    —
                                @endif
                            </span>
                        </td>
                        <td class="text-center">
                            <span class="po-count-badge">{{ $vendor->purchase_orders_count }}</span>
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('vendors.show', $vendor->id) }}" class="btn btn-sm btn-light" style="border: 1px solid rgba(0,0,0,0.1); font-weight: 600;">Open</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i data-feather="users" style="width: 48px; height: 48px; color: var(--text-muted); opacity: 0.5; margin-bottom: 16px;"></i>
                            <h5 style="font-family: 'Outfit'; font-weight: 600; color: var(--text-main);">No Vendors Found</h5>
                            <p class="text-muted" style="max-width: 300px; margin: 0 auto;">Add your first vendor to start tracking procurement.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($vendors->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $vendors->links() }}
    </div>
    @endif
</div>
@endsection
