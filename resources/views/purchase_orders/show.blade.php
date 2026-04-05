@extends('layouts.app')

@push('styles')
<style>
    .page-header { margin-bottom: 32px; }
    .glass-card { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.8); border-radius: var(--radius-lg); padding: 32px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 24px; }
    .status-badge { padding: 8px 16px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 6px; }
    .badge-Draft { background: rgba(100, 116, 139, 0.1); color: #475569; border: 1px solid rgba(100,116,139,0.2); }
    .badge-Sent { background: rgba(56, 189, 248, 0.1); color: #0284c7; border: 1px solid rgba(56,189,248,0.2); }
    .badge-Approved { background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34,197,94,0.2); }
    .badge-Cancelled { background: rgba(239, 68, 68, 0.1); color: #dc2626; border: 1px solid rgba(239,68,68,0.2); }
    .section-title { font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.25rem; display: flex; align-items: center; gap: 8px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid rgba(0,0,0,0.03); }
    .info-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; }
    .info-value { font-size: 1.05rem; font-weight: 600; color: var(--text-main); border: 1px solid rgba(0,0,0,0.05); padding: 12px 16px; border-radius: 8px; background: rgba(255,255,255,0.7); box-shadow: inset 0 2px 4px rgba(0,0,0,0.01); }
    .qty-badge { background: rgba(99,102,241,0.1); color: var(--primary); font-family: 'Outfit'; font-weight: 700; padding: 6px 14px; border-radius: 8px; font-size: 1.05rem; }
    .cost-badge { font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1rem; }
    .total-card { background: linear-gradient(135deg, rgba(99,102,241,0.08), rgba(139,92,246,0.08)); border: 1px solid rgba(99,102,241,0.15); border-radius: 12px; padding: 20px; }
    .total-label { font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; }
    .total-value { font-family: 'Outfit'; font-weight: 800; font-size: 2rem; color: var(--primary); letter-spacing: -1px; }
    .receipt-link-card { background: rgba(34,197,94,0.05); border: 1px solid rgba(34,197,94,0.2); border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; gap: 12px; }
    .ref-link { color: var(--primary); font-weight: 700; font-family: 'Outfit'; text-decoration: none; transition: color 0.2s; }
    .ref-link:hover { color: var(--secondary); text-decoration: underline; }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <a href="{{ route('purchase-orders.index') }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;"><i data-feather="arrow-left" style="width: 14px;"></i> Back to Purchase Orders</a>
            <div class="d-flex align-items-center gap-3 mt-2">
                <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">{{ $purchaseOrder->reference_no }}</h2>
                <span class="status-badge badge-{{ $purchaseOrder->status }}">{{ $purchaseOrder->status }}</span>
            </div>
        </div>
        
        <div class="d-flex flex-wrap gap-2">
            @if($purchaseOrder->status === 'Draft' || $purchaseOrder->status === 'Sent')
                @if(auth()->user() && auth()->user()->isManagerOrAbove())
                <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2 fw-bold" style="height: 48px; border-radius: 10px;">
                    <i data-feather="edit-2" style="width: 16px;"></i> Edit
                </a>
                @endif
                
                @if(auth()->user() && auth()->user()->isAdmin())
                <form action="{{ route('purchase-orders.destroy', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Delete this purchase order permanently?');" class="m-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger d-flex align-items-center gap-2 fw-bold" style="height: 48px; border-radius: 10px;">
                        <i data-feather="trash-2" style="width: 16px;"></i> Delete
                    </button>
                </form>
                @endif

                @if(auth()->user() && auth()->user()->isManagerOrAbove())
                <form action="{{ route('purchase-orders.cancel', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Cancel this purchase order?');" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-outline-warning d-flex align-items-center gap-2 fw-bold" style="height: 48px; border-radius: 10px;">
                        <i data-feather="x-circle" style="width: 16px;"></i> Cancel PO
                    </button>
                </form>

                <form action="{{ route('purchase-orders.approve', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Approve this PO and auto-generate a Receipt draft?');" class="m-0 ms-2">
                    @csrf
                    <button type="submit" class="btn btn-success d-flex align-items-center gap-2 fw-bold" style="height: 48px; border-radius: 10px; background: #10b981; border: none; box-shadow: 0 4px 12px rgba(16,185,129,0.3);">
                        <i data-feather="check-circle" style="width: 18px;"></i> Approve & Generate Receipt
                    </button>
                </form>
                @endif
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="glass-card h-100">
                <h6 class="section-title"><i data-feather="truck" style="color: var(--primary);"></i> Order Details</h6>
                
                <div class="mb-4">
                    <div class="info-label">Vendor</div>
                    <div class="info-value d-flex align-items-center gap-2">
                        <i data-feather="briefcase" style="width: 16px; color: var(--text-muted);"></i>
                        <a href="{{ route('vendors.show', $purchaseOrder->vendor) }}" class="ref-link">{{ $purchaseOrder->vendor->name }}</a>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="info-label">Expected Delivery Date</div>
                    <div class="info-value d-flex align-items-center gap-2">
                        <i data-feather="calendar" style="width: 16px; color: var(--text-muted);"></i>
                        {{ $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('l, F d, Y') : 'Unspecified' }}
                    </div>
                </div>

                @if($purchaseOrder->notes)
                <div class="mb-4">
                    <div class="info-label">Notes</div>
                    <div class="info-value">
                        {{ $purchaseOrder->notes }}
                    </div>
                </div>
                @endif

                <!-- Cost Summary -->
                <div class="total-card mt-4">
                    <div class="total-label">Total Order Cost</div>
                    <div class="total-value">₹{{ number_format($purchaseOrder->total_cost, 2) }}</div>
                    <div class="text-muted" style="font-size: 0.85rem; margin-top: 4px;">{{ $purchaseOrder->total_quantity }} units total</div>
                </div>

                <!-- Linked Receipt (if approved) -->
                @if($purchaseOrder->receipt)
                <div class="receipt-link-card mt-4">
                    <i data-feather="link" style="width: 20px; color: #16a34a;"></i>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase;">Auto-Generated Receipt</div>
                        <a href="{{ route('receipts.show', $purchaseOrder->receipt) }}" class="ref-link" style="font-size: 1.1rem;">
                            {{ $purchaseOrder->receipt->reference_no }}
                        </a>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="col-lg-8">
            <div class="glass-panel overflow-hidden h-100">
                <div class="p-4 border-bottom" style="background: rgba(255,255,255,0.4);">
                    <h6 class="m-0" style="font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
                        <i data-feather="package" style="color: var(--secondary); width: 20px;"></i> Order Line Items
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: rgba(0,0,0,0.02);">
                            <tr>
                                <th class="ps-4 border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">PRODUCT</th>
                                <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">SKU</th>
                                <th class="border-0 text-center" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">QTY</th>
                                <th class="border-0 text-end" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">UNIT COST</th>
                                <th class="border-0 text-end pe-4" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">LINE TOTAL</th>
                            </tr>
                        </thead>
                        <tbody style="border-top: 2px solid rgba(0,0,0,0.05);">
                            @foreach($purchaseOrder->items as $item)
                            <tr>
                                <td class="ps-4 fw-bold" style="color: var(--text-main); font-size: 1.05rem;">{{ $item->product->name }}</td>
                                <td><span style="font-family: 'Outfit'; font-weight: 600; padding: 4px 8px; background: rgba(0,0,0,0.04); border-radius: 6px; color: var(--text-muted);">{{ $item->product->sku }}</span></td>
                                <td class="text-center">
                                    <span class="qty-badge">{{ $item->quantity }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="cost-badge">₹{{ number_format($item->unit_cost, 2) }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="cost-badge" style="color: var(--primary);">₹{{ number_format($item->line_total, 2) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot style="border-top: 2px solid rgba(0,0,0,0.08);">
                            <tr style="background: rgba(99,102,241,0.03);">
                                <td colspan="2" class="ps-4 fw-bold" style="font-family: 'Outfit'; color: var(--text-main);">TOTAL</td>
                                <td class="text-center">
                                    <span class="qty-badge">{{ $purchaseOrder->total_quantity }}</span>
                                </td>
                                <td></td>
                                <td class="text-end pe-4">
                                    <span style="font-family: 'Outfit'; font-weight: 800; font-size: 1.15rem; color: var(--primary);">₹{{ number_format($purchaseOrder->total_cost, 2) }}</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
