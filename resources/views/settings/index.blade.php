@extends('layouts.app')

@push('styles')
<style>
    .page-header {
        margin-bottom: 32px;
    }
    .form-glass {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid rgba(0,0,0,0.05);
        border-radius: 8px;
        padding: 10px 14px;
        height: 44px;
        transition: all 0.2s;
    }
    .form-glass:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(99,102,241,0.1);
    }
    .code-badge {
        font-family: 'Outfit';
        font-weight: 700;
        letter-spacing: 1px;
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .type-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.70rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .type-internal { background: rgba(99, 102, 241, 0.1); color: var(--primary); border: 1px solid rgba(99,102,241,0.2); }
    .type-vendor { background: rgba(245, 158, 11, 0.1); color: #d97706; border: 1px solid rgba(245,158,11,0.2); }
    .type-customer { background: rgba(16, 185, 129, 0.1); color: #059669; border: 1px solid rgba(16,185,129,0.2); }
    .type-inventory_loss { background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239,68,68,0.2); }
    .action-btn {
        width: 32px; height: 32px;
        display: inline-flex; justify-content: center; align-items: center;
        border-radius: 8px;
        transition: all 0.2s;
        border: 1px solid transparent;
        background: transparent;
    }
    .action-btn.del { color: #ef4444; }
    .action-btn.del:hover { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.2); }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header">
        <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Infrastructure Settings</h2>
        <p class="text-muted m-0 mt-1" style="font-size: 1rem;">Architect physical warehouses and set operational zones.</p>
    </div>

    <div class="row g-4">
        <!-- WAREHOUSES COLUMN -->
        <div class="col-lg-5">
            <div class="glass-panel p-4 mb-4" style="background: rgba(99, 102, 241, 0.03); border-color: rgba(99, 102, 241, 0.15);">
                <h5 class="fw-bold mb-4 d-flex align-items-center" style="font-family: 'Outfit'; color: var(--primary);">
                    <i data-feather="home" class="me-2" style="width: 22px;"></i> Register Warehouse
                </h5>
                <form action="{{ route('settings.warehouse.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Warehouse Name</label>
                        <input type="text" name="name" class="form-control form-glass" required placeholder="e.g. North Hub">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Hub Code Abbreviation</label>
                        <input type="text" name="code" class="form-control form-glass text-uppercase" style="font-family: 'Outfit'; font-weight: 600;" required placeholder="e.g. NHTB">
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Physical Address</label>
                        <textarea name="location_address" class="form-control form-glass" style="height: auto;" rows="2" placeholder="Optional reference location..."></textarea>
                    </div>
                    <button type="submit" class="btn w-100 d-flex justify-content-center align-items-center gap-2" style="background: rgba(15,23,42,0.9); color: white; font-weight: 600; height: 48px; border-radius: 12px; transition: background 0.2s;" onmouseover="this.style.background='var(--primary)'" onmouseout="this.style.background='rgba(15,23,42,0.9)'">
                        <i data-feather="save" style="width: 18px;"></i> Save Warehouse Base
                    </button>
                </form>
            </div>

            <div class="glass-panel overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: rgba(0,0,0,0.02);">
                            <tr>
                                <th class="ps-4 border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Code ID</th>
                                <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Warehouse Name</th>
                                <th class="text-end pe-4 border-0"></th>
                            </tr>
                        </thead>
                        <tbody style="border-top: 2px solid rgba(0,0,0,0.05);">
                            @forelse($warehouses as $wh)
                                <tr>
                                    <td class="ps-4"><span class="code-badge">{{ $wh->code }}</span></td>
                                    <td class="fw-bold" style="color: var(--text-main); font-size: 1.05rem;">{{ $wh->name }}</td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('settings.warehouse.destroy', $wh) }}" method="POST" onsubmit="return confirm('WARNING: Destroying a warehouse removes infrastructure context. Proceed?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn del" title="Delete Warehouse">
                                                <i data-feather="trash-2" style="width: 16px;"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-5 text-muted small">
                                    <i data-feather="box" style="width: 32px; height: 32px; opacity: 0.5; margin-bottom: 12px;"></i><br>
                                    No Warehouses configured yet.
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- LOCATIONS COLUMN -->
        <div class="col-lg-7">
            <div class="glass-panel p-4 mb-4">
                <h5 class="fw-bold mb-4 d-flex align-items-center" style="font-family: 'Outfit'; color: var(--text-main);">
                    <i data-feather="map-pin" class="me-2" style="width: 22px; color: var(--secondary);"></i> Expand Topology (Add Zones)
                </h5>
                <form action="{{ route('settings.location.store') }}" method="POST" class="row border-start border-4 ps-4 py-2 ms-0 g-4" style="border-color: rgba(99,102,241,0.3) !important;">
                    @csrf
                    <div class="col-md-5">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Parent Warehouse</label>
                        <select name="warehouse_id" class="form-select form-glass" required>
                            <option value="">Select Target...</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Zone / Location Name</label>
                        <input type="text" name="name" class="form-control form-glass" required placeholder="e.g. Picking Aisle 1">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Classification Type</label>
                        <select name="type" class="form-select form-glass" required>
                            <option value="internal">Internal (Storage)</option>
                            <option value="vendor">Vendor (Supplier bounds)</option>
                            <option value="customer">Customer (Dispatch bounds)</option>
                            <option value="inventory_loss">Inventory Loss (Virtual)</option>
                        </select>
                    </div>
                    <div class="col-md-7 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 fw-bold d-flex justify-content-center align-items-center gap-2" style="height: 44px; border-radius: 10px;">
                            <i data-feather="layers" style="width: 16px;"></i> Map Logistics Zone
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="glass-panel overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: rgba(0,0,0,0.02);">
                            <tr>
                                <th class="ps-4 border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Parent Hub</th>
                                <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Location Tag</th>
                                <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Class Type</th>
                                <th class="text-end pe-4 border-0"></th>
                            </tr>
                        </thead>
                        <tbody style="border-top: 2px solid rgba(0,0,0,0.05);">
                            @foreach($warehouses as $wh)
                                @foreach($wh->locations as $loc)
                                <tr>
                                    <td class="ps-4">
                                        <span class="code-badge" style="background: rgba(0,0,0,0.05); color: var(--text-muted); box-shadow: none;">{{ $wh->code }}</span>
                                    </td>
                                    <td class="fw-semibold" style="color: var(--text-main);">{{ $loc->name }}</td>
                                    <td>
                                        <span class="type-badge type-{{ $loc->type }}">
                                            {{ str_replace('_', ' ', $loc->type) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form action="{{ route('settings.location.destroy', $loc) }}" method="POST" onsubmit="return confirm('Delete this zone mapping?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn del" title="Delete Zone">
                                                <i data-feather="trash-2" style="width: 16px;"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
