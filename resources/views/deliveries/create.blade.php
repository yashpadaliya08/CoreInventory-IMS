@extends('layouts.app')

@push('styles')
<style>
    .page-header { margin-bottom: 32px; }
    .glass-form-card { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.8); border-radius: var(--radius-lg); padding: 32px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 24px; }
    .form-glass { background: rgba(255, 255, 255, 0.9); border: 1px solid rgba(0,0,0,0.05); border-radius: 8px; padding: 12px 16px; height: 48px; transition: all 0.2s; }
    .form-glass:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    .item-row { background: rgba(255,255,255,0.5); padding: 16px; border-radius: 12px; border: 1px solid rgba(0,0,0,0.03); margin-bottom: 12px; transition: all 0.2s; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
    .item-row:hover { background: rgba(255,255,255,0.9); box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .section-title { font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.25rem; display: flex; align-items: center; gap: 8px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid rgba(0,0,0,0.03); }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('deliveries.index') }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;"><i data-feather="arrow-left" style="width: 14px;"></i> Back to Deliveries</a>
            <h2 class="m-0 mt-2" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Schedule New Delivery</h2>
        </div>
    </div>

    <form action="{{ route('deliveries.store') }}" method="POST">
        @csrf
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="glass-form-card h-100">
                    <h6 class="section-title"><i data-feather="info" style="color: var(--primary);"></i> Dispatch Header</h6>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Customer Identity</label>
                        <div class="position-relative">
                            <i data-feather="users" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <input type="text" name="customer_name" class="form-control form-glass" style="padding-left: 42px;" placeholder="e.g. Acme Corp">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Scheduled Ship Date</label>
                        <div class="position-relative">
                            <i data-feather="calendar" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <input type="date" name="scheduled_date" class="form-control form-glass" style="padding-left: 42px;">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="glass-form-card">
                    <h6 class="section-title"><i data-feather="truck" style="color: var(--secondary);"></i> Products to Dispatch</h6>
                    
                    <div id="delivery-items">
                        <div class="row g-3 item-row delivery-item align-items-end">
                            <div class="col-md-8">
                                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Target Product</label>
                                <select name="items[0][product_id]" class="form-select form-glass" required>
                                    <option value="">Select Product...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} (SKU: {{ $product->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Dispatch Qty</label>
                                <div class="position-relative">
                                    <i data-feather="hash" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                    <input type="number" name="items[0][quantity]" class="form-control form-glass" style="padding-left: 42px; font-family: 'Outfit'; font-weight: 700; font-size: 1.1rem;" required min="1" placeholder="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-2 mt-3" style="border-radius: 8px; font-weight: 600;" onclick="addDeliveryItem()">
                        <i data-feather="plus-circle" style="width: 16px;"></i> Add Row
                    </button>
                </div>
                
                <div class="d-flex justify-content-end mb-5">
                    <button type="submit" class="btn btn-primary d-flex justify-content-center align-items-center gap-2" style="height: 54px; padding: 0 40px; font-size: 1.1rem; border-radius: 12px; box-shadow: 0 8px 20px rgba(99,102,241,0.3);">
                        <i data-feather="save"></i> Schedule Delivery Run
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let itemIndex = 1;
    function addDeliveryItem() {
        const container = document.getElementById('delivery-items');
        const firstItem = container.querySelector('.delivery-item');
        const newItem = firstItem.cloneNode(true);
        
        newItem.querySelector('select').name = `items[${itemIndex}][product_id]`;
        newItem.querySelector('select').value = '';
        newItem.querySelector('input').name = `items[${itemIndex}][quantity]`;
        newItem.querySelector('input').value = '';
        
        container.appendChild(newItem);
        if(typeof feather !== 'undefined') feather.replace();
        itemIndex++;
    }
</script>
@endpush
@endsection
