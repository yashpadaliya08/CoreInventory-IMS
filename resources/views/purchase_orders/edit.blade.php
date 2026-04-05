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
    textarea.form-glass { height: auto; min-height: 80px; }
    .remove-row-btn { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #dc2626; border-radius: 8px; width: 44px; height: 48px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
    .remove-row-btn:hover { background: rgba(239,68,68,0.2); }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;"><i data-feather="arrow-left" style="width: 14px;"></i> Back to Document</a>
            <h2 class="m-0 mt-2" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Edit {{ $purchaseOrder->reference_no }}</h2>
        </div>
    </div>

    <form action="{{ route('purchase-orders.update', $purchaseOrder) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="glass-form-card h-100">
                    <h6 class="section-title"><i data-feather="edit-2" style="color: var(--primary);"></i> Order Details</h6>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Vendor *</label>
                        <div class="position-relative">
                            <i data-feather="briefcase" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <select name="vendor_id" class="form-select form-glass" style="padding-left: 42px;" required>
                                <option value="">Select Vendor...</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}" {{ old('vendor_id', $purchaseOrder->vendor_id) == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Expected Delivery</label>
                        <div class="position-relative">
                            <i data-feather="calendar" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <input type="date" name="expected_date" class="form-control form-glass" style="padding-left: 42px;" value="{{ old('expected_date', $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Notes</label>
                        <div class="position-relative">
                            <i data-feather="file-text" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <textarea name="notes" class="form-control form-glass" style="padding-left: 42px; padding-top: 14px;" rows="3">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                        </div>
                    </div>

                    <div style="background: rgba(99,102,241,0.05); border: 1px solid rgba(99,102,241,0.15); border-radius: 12px; padding: 20px; margin-top: 16px;">
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Total Cost</span>
                            <span id="total-cost-display" style="font-family: 'Outfit'; font-weight: 800; font-size: 1.5rem; color: var(--primary);">₹0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="glass-form-card">
                    <h6 class="section-title"><i data-feather="package" style="color: var(--secondary);"></i> Order Line Items</h6>
                    
                    <div id="po-items">
                        @foreach($purchaseOrder->items as $idx => $existingItem)
                        <div class="row g-3 item-row po-item align-items-end">
                            <div class="col-md-5">
                                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Product</label>
                                <select name="items[{{ $idx }}][product_id]" class="form-select form-glass" required>
                                    <option value="">Select Product...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ $existingItem->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }} ({{ $product->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Quantity</label>
                                <div class="position-relative">
                                    <i data-feather="hash" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                    <input type="number" name="items[{{ $idx }}][quantity]" class="form-control form-glass item-qty" style="padding-left: 42px; font-family: 'Outfit'; font-weight: 700;" required min="1" value="{{ $existingItem->quantity }}" oninput="updateTotal()">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Unit Cost (₹)</label>
                                <div class="position-relative">
                                    <i data-feather="dollar-sign" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                    <input type="number" step="0.01" name="items[{{ $idx }}][unit_cost]" class="form-control form-glass item-cost" style="padding-left: 42px; font-family: 'Outfit'; font-weight: 700;" required min="0" value="{{ $existingItem->unit_cost }}" oninput="updateTotal()">
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="remove-row-btn" onclick="removeItem(this)" {{ $idx === 0 ? 'style=visibility:hidden' : '' }}>
                                    <i data-feather="x" style="width: 16px;"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-2 mt-3" style="border-radius: 8px; font-weight: 600;" onclick="addItem()">
                        <i data-feather="plus-circle" style="width: 16px;"></i> Add Row
                    </button>
                </div>
                
                <div class="d-flex justify-content-end mb-5">
                    <button type="submit" class="btn btn-primary d-flex justify-content-center align-items-center gap-2" style="height: 54px; padding: 0 40px; font-size: 1.1rem; border-radius: 12px; box-shadow: 0 8px 20px rgba(99,102,241,0.3);">
                        <i data-feather="save"></i> Update Purchase Order
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let itemIndex = {{ $purchaseOrder->items->count() }};

    function addItem() {
        const container = document.getElementById('po-items');
        const firstItem = container.querySelector('.po-item');
        const newItem = firstItem.cloneNode(true);
        
        newItem.querySelector('select').name = `items[${itemIndex}][product_id]`;
        newItem.querySelector('select').value = '';
        
        const inputs = newItem.querySelectorAll('input');
        inputs[0].name = `items[${itemIndex}][quantity]`;
        inputs[0].value = '';
        inputs[1].name = `items[${itemIndex}][unit_cost]`;
        inputs[1].value = '';
        
        const removeBtn = newItem.querySelector('.remove-row-btn');
        if (removeBtn) removeBtn.style.visibility = 'visible';
        
        container.appendChild(newItem);
        if(typeof feather !== 'undefined') feather.replace();
        itemIndex++;
    }

    function removeItem(btn) {
        const row = btn.closest('.po-item');
        const container = document.getElementById('po-items');
        if (container.querySelectorAll('.po-item').length > 1) {
            row.remove();
            updateTotal();
        }
    }

    function updateTotal() {
        let total = 0;
        const rows = document.querySelectorAll('.po-item');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty')?.value) || 0;
            const cost = parseFloat(row.querySelector('.item-cost')?.value) || 0;
            total += qty * cost;
        });
        document.getElementById('total-cost-display').textContent = '₹' + total.toFixed(2);
    }

    // Calculate initial total on page load
    document.addEventListener('DOMContentLoaded', updateTotal);
</script>
@endpush
@endsection
