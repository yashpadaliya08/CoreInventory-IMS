@extends('layouts.app')

@push('styles')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; padding: 24px 32px; background: var(--bg-surface); backdrop-filter: blur(16px); border: var(--glass-border); border-radius: var(--radius-lg); box-shadow: var(--glass-shadow); }
    .alert-card { padding: 24px; border-radius: var(--radius-lg); background: var(--bg-surface); border: var(--glass-border); backdrop-filter: blur(16px); box-shadow: var(--glass-shadow); transition: all 0.3s ease; position: relative; overflow: hidden; }
    .alert-card:hover { transform: translateY(-3px); box-shadow: 0 12px 30px rgba(220, 38, 38, 0.1); border-color: rgba(220, 38, 38, 0.3); }
    .alert-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: #dc2626; border-radius: 4px 0 0 4px; }
    
    .sku-badge { background: rgba(99,102,241,0.1); color: var(--primary); padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; font-family: 'Outfit'; border: 1px solid rgba(99,102,241,0.2); }
    
    .stock-indicator { display: flex; align-items: center; gap: 16px; margin: 16px 0; padding: 12px 16px; background: rgba(0,0,0,0.02); border-radius: 8px; border: 1px solid rgba(0,0,0,0.05); }
    .stock-indicator-dark { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); }
    
    .stock-val { flex: 1; text-align: center; }
    .stock-val-num { font-size: 1.8rem; font-weight: 700; font-family: 'Outfit'; line-height: 1; margin-bottom: 4px; }
    .stock-val-lbl { font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1px; }
    
    .deficit-val { font-size: 1.2rem; font-weight: 700; color: #dc2626; font-family: 'Outfit'; }
    
    .vendor-box { border-top: 1px solid rgba(0,0,0,0.08); padding-top: 16px; margin-top: 16px; display: flex; justify-content: space-between; align-items: center; }
    
    [data-theme="dark"] .vendor-box { border-color: rgba(255,255,255,0.08); }
    [data-theme="dark"] .stock-indicator { background: rgba(255,255,255,0.02); border-color: rgba(255,255,255,0.05); }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <div class="d-flex align-items-center gap-3">
                <div style="background: rgba(220, 38, 38, 0.1); color: #dc2626; padding: 12px; border-radius: 12px;">
                    <i data-feather="bell"></i>
                </div>
                <div>
                    <h2 class="m-0" style="font-size: 1.8rem; letter-spacing: -0.5px;">Low Stock Alerts</h2>
                    <p class="text-muted m-0 mt-1" style="font-size: 0.95rem;">Products that have fallen below their safe reorder threshold.</p>
                </div>
            </div>
        </div>
        <div>
            <span class="badge bg-danger px-3 py-2" style="font-size: 0.9rem; font-family: 'Outfit'; border-radius: 8px;">
                {{ $lowStockProducts->count() }} ALERTS ACTIVE
            </span>
        </div>
    </div>

    @if($lowStockProducts->isEmpty())
        <!-- Empty State -->
        <div class="glass-panel d-flex flex-column align-items-center justify-content-center text-center p-5" style="min-height: 400px;">
            <div style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 24px; border-radius: 50%; margin-bottom: 24px;">
                <i data-feather="check-circle" style="width: 48px; height: 48px; stroke-width: 1.5;"></i>
            </div>
            <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-main);">All Stock Levels Healthy</h3>
            <p class="text-muted mb-0" style="font-size: 1rem; max-width: 400px;">
                There are no active low stock alerts. All product inventory is currently sitting above their required reorder levels.
            </p>
        </div>
    @else
        <!-- Alert Grid -->
        <div class="row g-4">
            @foreach($lowStockProducts as $item)
                <div class="col-xl-4 col-md-6">
                    <div class="alert-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h4 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 6px; line-height: 1.3;">{{ $item->name }}</h4>
                                <span class="sku-badge">{{ $item->sku }}</span>
                            </div>
                        </div>
                        
                        <div class="stock-indicator">
                            <div class="stock-val">
                                <div class="stock-val-num" style="color: #dc2626;">{{ $item->current_stock }}</div>
                                <div class="stock-val-lbl">Current</div>
                            </div>
                            <div style="width: 1px; height: 30px; background: rgba(0,0,0,0.1);"></div>
                            <div class="stock-val">
                                <div class="stock-val-num" style="color: #f59e0b;">{{ $item->reorder_level }}</div>
                                <div class="stock-val-lbl">Min Level</div>
                            </div>
                            <div style="width: 1px; height: 30px; background: rgba(0,0,0,0.1);"></div>
                            <div class="stock-val">
                                <div class="deficit-val">-{{ $item->deficit }}</div>
                                <div class="stock-val-lbl">Deficit</div>
                            </div>
                        </div>

                        <!-- Vendor Quick Action -->
                        <div class="vendor-box">
                            @if($item->suggested_vendor)
                                <div>
                                    <div style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Last Supplied By</div>
                                    <div style="font-size: 0.9rem; font-weight: 600; color: var(--text-main);">{{ $item->suggested_vendor->vendor_name }}</div>
                                </div>
                                <form action="{{ route('alerts.quickReorder') }}" method="POST" class="m-0 p-0">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $item->id }}">
                                    <input type="hidden" name="vendor_id" value="{{ $item->suggested_vendor->vendor_id }}">
                                    <!-- Recommend ordering the deficit + 20% buffer -->
                                    <input type="hidden" name="quantity" value="{{ ceil($item->deficit + ($item->reorder_level * 0.2)) }}">
                                    <button type="submit" class="btn btn-sm btn-primary" style="font-weight: 600; padding: 6px 12px; border-radius: 8px;">
                                        <i data-feather="zap" style="width: 14px; height: 14px; margin-right: 4px;"></i> Quick PO
                                    </button>
                                </form>
                            @else
                                <div>
                                    <div style="font-size: 0.7rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Supplier Status</div>
                                    <div style="font-size: 0.9rem; font-weight: 600; color: #f59e0b;">No Past Vendor Found</div>
                                </div>
                                <a href="{{ route('purchase-orders.create') }}" class="btn btn-sm btn-secondary" style="font-weight: 600; padding: 6px 12px; border-radius: 8px;">
                                    Manual PO
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
