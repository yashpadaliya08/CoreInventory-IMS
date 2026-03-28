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
    .badge-category { background: rgba(99, 102, 241, 0.1); color: var(--primary); }
    .sku-link { color: var(--text-main); font-weight: 700; text-decoration: none; font-family: 'Outfit'; transition: color 0.2s; }
    .sku-link:hover { color: var(--primary); }
    .row-alert { background: rgba(254, 226, 226, 0.6); }
    .row-alert:hover { background: rgba(254, 226, 226, 0.9) !important; }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Products Master</h2>
            <p class="text-muted m-0 mt-1" style="font-size: 1rem;">Manage your catalog, stock limits, and categories.</p>
        </div>
        @if(auth()->user() && auth()->user()->isManagerOrAbove())
        <a href="{{ route('products.create') }}" class="btn btn-primary d-flex align-items-center gap-2" style="height: 44px; padding: 0 24px;">
            <i data-feather="plus" style="width: 18px;"></i> Create Product
        </a>
        @endif
    </div>

    <div class="filter-panel">
        <form method="GET" action="{{ route('products.index') }}" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Search Query</label>
                <div class="position-relative">
                    <i data-feather="search" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                    <input type="text" name="search" class="form-control" style="padding-left: 42px; height: 44px; background: rgba(255,255,255,0.9);" placeholder="Search by product name or SKU..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Category Filter</label>
                <div class="position-relative">
                    <i data-feather="tag" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                    <select name="category" class="form-select" style="padding-left: 42px; height: 44px; background: rgba(255,255,255,0.9);">
                        <option value="">All Available Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-secondary w-100 d-flex justify-content-center align-items-center gap-2" style="height: 44px; background: rgba(0,0,0,0.05); color: var(--text-main); border: 1px solid rgba(0,0,0,0.1); font-weight: 600;">
                    <i data-feather="filter" style="width: 16px;"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    <div class="glass-panel overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead style="background: rgba(0,0,0,0.02);">
                    <tr>
                        <th class="ps-4 border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">SKU Number</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Product Details</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Category</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Unit</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Total Stock</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">State</th>
                    </tr>
                </thead>
                <tbody style="border-top: 2px solid rgba(0,0,0,0.05);">
                    @forelse($products as $product)
                    @php $isLowStock = $product->total_stock < $product->reorder_level; @endphp
                    <tr class="{{ $isLowStock ? 'row-alert' : '' }}" style="transition: background 0.2s;">
                        <td class="ps-4">
                            <a href="{{ route('products.show', $product->id) }}" class="sku-link">#{{ $product->sku }}</a>
                        </td>
                        <td>
                            <div class="fw-semibold" style="color: var(--text-main); font-size: 1.05rem;"><a href="{{ route('products.show', $product->id) }}" style="color: inherit; text-decoration: none;">{{ $product->name }}</a></div>
                            <div class="text-muted" style="font-size: 0.8rem;">Reorder trigger at {{ $product->reorder_level }}</div>
                        </td>
                        <td><span class="status-badge badge-category">{{ $product->category }}</span></td>
                        <td><span style="font-family: 'Outfit'; font-weight: 600; padding: 4px 8px; background: rgba(0,0,0,0.04); border-radius: 6px; color: var(--text-muted);">{{ $product->unit_of_measure }}</span></td>
                        <td>
                            <span style="font-family: 'Outfit'; font-size: 1.25rem; font-weight: 700; color: {{ $isLowStock ? '#dc2626' : 'var(--text-main)' }};">{{ $product->total_stock }}</span>
                        </td>
                        <td>
                            @if($isLowStock)
                                <span class="status-badge" style="background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239,68,68,0.2);">
                                    <i data-feather="alert-circle" style="width: 14px;"></i> Low Level
                                </span>
                            @else
                                <span class="status-badge" style="background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34,197,94,0.2);">
                                    <i data-feather="check" style="width: 14px;"></i> Healthy
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i data-feather="inbox" style="width: 48px; height: 48px; color: var(--text-muted); opacity: 0.5; margin-bottom: 16px;"></i>
                            <h5 style="font-family: 'Outfit'; font-weight: 600; color: var(--text-main);">No products found</h5>
                            <p class="text-muted" style="max-width: 300px; margin: 0 auto;">You haven't added any products yet, or none match your search criteria.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4" style="opacity: 0.9;">
        @if(method_exists($products, 'links'))
            {{ $products->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
@endsection
