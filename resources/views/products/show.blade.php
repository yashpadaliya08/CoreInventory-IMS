@extends('layouts.app')

@push('styles')
<style>
    .page-header { margin-bottom: 32px; }
    .glass-card { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.8); border-radius: var(--radius-lg); padding: 32px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 24px; }
    .section-title { font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.25rem; display: flex; align-items: center; gap: 8px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid rgba(0,0,0,0.03); }
    .stat-box { background: rgba(255,255,255,0.9); border: 1px solid rgba(0,0,0,0.05); border-radius: 16px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); text-align: center; }
    .stat-label { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--text-muted); letter-spacing: 1px; margin-bottom: 8px; }
    .stat-val { font-family: 'Outfit'; font-size: 2.25rem; font-weight: 800; color: var(--text-main); line-height: 1; }
    .loc-badge { font-family: 'Outfit'; font-weight: 600; padding: 4px 10px; background: rgba(0,0,0,0.04); border-radius: 8px; font-size: 0.85rem;}
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <a href="{{ route('products.index') }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;"><i data-feather="arrow-left" style="width: 14px;"></i> Back to Database</a>
            <div class="d-flex align-items-center gap-3 mt-2">
                <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">{{ $product->name }}</h2>
                <span class="loc-badge" style="background: rgba(99,102,241,0.1); color: var(--primary);">SKU: {{ $product->sku }}</span>
            </div>
        </div>
        
        <div class="d-flex flex-wrap gap-2">
            @if(auth()->user() && auth()->user()->isManagerOrAbove())
                <a href="{{ route('products.edit', $product) }}" class="btn btn-primary d-flex align-items-center gap-2 fw-bold" style="height: 48px; border-radius: 10px; box-shadow: 0 4px 12px rgba(99,102,241,0.3);">
                    <i data-feather="edit-2" style="width: 16px;"></i> Edit Profile
                </a>
            @endif
            @if(auth()->user() && auth()->user()->isAdmin())
                <form action="{{ route('products.destroy', $product) }}" method="POST" onsubmit="return confirm('Delete this product? All unvalidated orders matching this product may fail. Proceed?');" class="m-0 ms-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger d-flex align-items-center gap-2 fw-bold" style="height: 48px; border-radius: 10px;">
                        <i data-feather="trash-2" style="width: 16px;"></i> Delete
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="row g-4 d-flex align-items-stretch">
        <div class="col-lg-5">
            <div class="glass-card h-100 mb-0">
                <h6 class="section-title"><i data-feather="bar-chart-2" style="color: var(--primary);"></i> Aggregated Stock Metrics</h6>
                
                <div class="row g-3">
                    <div class="col-12">
                        @php $isLow = $product->total_stock < $product->reorder_level; @endphp
                        <div class="stat-box" style="{{ $isLow ? 'background: rgba(239, 68, 68, 0.05); border-color: rgba(239, 68, 68, 0.2);' : '' }}">
                            <div class="stat-label" style="{{ $isLow ? 'color: #dc2626;' : '' }}">Real-Time Network Stock</div>
                            <div class="stat-val d-flex justify-content-center align-items-center gap-2" style="{{ $isLow ? 'color: #dc2626;' : '' }}">
                                {{ $product->total_stock }} <span style="font-size: 1rem; font-weight: 600; color: var(--text-muted); padding-top: 12px;">{{ $product->unit_of_measure }}</span>
                            </div>
                            @if($isLow)
                                <div class="text-danger small fw-bold mt-2"><i data-feather="alert-triangle" style="width: 14px;"></i> Sub-Threshold Warning</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box">
                            <div class="stat-label">Category Group</div>
                            <div class="stat-val" style="font-size: 1.25rem; font-family: 'Inter'; padding-top: 8px;">{{ $product->category ?? 'N/A' }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-box">
                            <div class="stat-label">Reorder Restock Level</div>
                            <div class="stat-val" style="font-size: 1.5rem; padding-top: 4px;">{{ $product->reorder_level }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="glass-card h-100 mb-0 d-flex flex-column" style="background: rgba(99,102,241,0.02); border-color: rgba(99,102,241,0.1); padding: 0;">
                <div class="p-4 border-bottom" style="background: rgba(255,255,255,0.4);">
                    <h6 class="m-0" style="font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
                        <i data-feather="map" style="color: var(--secondary); width: 20px;"></i> Network Topology Breakdown
                    </h6>
                </div>
                
                <div class="table-responsive flex-grow-1">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: rgba(0,0,0,0.02);">
                            <tr>
                                <th class="ps-4 border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Location Identity</th>
                                <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Zone Class</th>
                                <th class="border-0 text-end pe-4" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Physical Quantity</th>
                            </tr>
                        </thead>
                        <tbody style="border-top: 2px solid rgba(0,0,0,0.05);">
                            @forelse($stockByLocation as $stock)
                            <tr>
                                <td class="ps-4 fw-bold" style="color: var(--text-main); font-size: 1.05rem;">{{ $stock->location->name }}</td>
                                <td><span class="loc-badge">{{ ucfirst($stock->location->type) }}</span></td>
                                <td class="text-end pe-4" style="font-family: 'Outfit'; font-size: 1.2rem; font-weight: 700;">
                                    {{ $stock->total_stock }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <i data-feather="box" style="width: 48px; height: 48px; color: var(--text-muted); opacity: 0.3; margin-bottom: 16px;"></i>
                                    <h6 style="font-family: 'Outfit'; font-weight: 700; color: var(--text-main);">Zero Traceability Found</h6>
                                    <p class="text-muted small mb-0">This product is currently out of stock across the entire network architecture.</p>
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
