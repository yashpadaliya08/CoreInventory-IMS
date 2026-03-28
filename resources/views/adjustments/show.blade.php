@extends('layouts.app')

@push('styles')
<style>
    .page-header { margin-bottom: 32px; }
    .glass-card { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.8); border-radius: var(--radius-lg); padding: 32px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 24px; }
    .status-badge { padding: 8px 16px; border-radius: 20px; font-size: 0.8rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 6px; }
    .badge-Draft { background: rgba(100, 116, 139, 0.1); color: #475569; border: 1px solid rgba(100,116,139,0.2); }
    .badge-Done { background: rgba(34, 197, 94, 0.1); color: #16a34a; border: 1px solid rgba(34,197,94,0.2); }
    .section-title { font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.25rem; display: flex; align-items: center; gap: 8px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid rgba(0,0,0,0.03); }
    .info-label { font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 4px; }
    .info-value { font-size: 1.05rem; font-weight: 600; color: var(--text-main); border: 1px solid rgba(0,0,0,0.05); padding: 12px 16px; border-radius: 8px; background: rgba(255,255,255,0.7); box-shadow: inset 0 2px 4px rgba(0,0,0,0.01); }
    
    .delta-glass {
        background: rgba(255,255,255,0.8);
        border-radius: 16px;
        padding: 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        height: 100%;
    }
    .delta-title { font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: var(--text-muted); margin-bottom: 16px; }
    .delta-val { font-family: 'Outfit'; font-size: 3rem; font-weight: 800; line-height: 1; justify-content: center; display: flex; align-items: center; }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <a href="{{ route('adjustments.index') }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;"><i data-feather="arrow-left" style="width: 14px;"></i> Back to Adjustments</a>
            <div class="d-flex align-items-center gap-3 mt-2">
                <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Adjustment {{ $adjustment->reference_no }}</h2>
                <span class="status-badge badge-{{ $adjustment->status }}">{{ $adjustment->status }}</span>
            </div>
        </div>
        
        <div class="d-flex flex-wrap gap-2">
            @if($adjustment->status !== 'Done')
                @if(auth()->user() && auth()->user()->isManagerOrAbove())
                <form action="{{ route('adjustments.validate', $adjustment) }}" method="POST" onsubmit="return confirm('Validate this adjustment? This will permanently write the discrepancy to the stock ledger.');" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 fw-bold" style="height: 48px; border-radius: 10px; box-shadow: 0 4px 12px rgba(99,102,241,0.3);">
                        <i data-feather="check-circle" style="width: 18px;"></i> Validate Document
                    </button>
                </form>
                @endif
            @endif
        </div>
    </div>

    <div class="row g-4 d-flex align-items-stretch">
        <div class="col-lg-5">
            <div class="glass-card h-100 mb-0">
                <h6 class="section-title"><i data-feather="info" style="color: var(--primary);"></i> Adjustment Subject</h6>
                
                <div class="mb-4">
                    <div class="info-label">Correction Target Node</div>
                    <div class="info-value d-flex align-items-center gap-2">
                        <i data-feather="map-pin" style="width: 16px; color: var(--text-muted);"></i>
                        {{ $adjustment->location->name ?? 'N/A' }}
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="info-label">Product Name</div>
                    <div class="info-value d-flex align-items-center gap-2">
                        <i data-feather="box" style="width: 16px; color: var(--text-muted);"></i>
                        {{ $adjustment->product->name ?? 'N/A' }}
                    </div>
                </div>

                <div class="mb-2">
                    <div class="info-label">Product SKU Reference</div>
                    <div class="info-value d-flex align-items-center gap-2" style="font-family: 'Outfit'; background: rgba(0,0,0,0.03);">
                        <i data-feather="hash" style="width: 16px; color: var(--text-muted);"></i>
                        {{ $adjustment->product->sku ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="glass-card h-100 mb-0 d-flex flex-column" style="background: rgba(99,102,241,0.02); border-color: rgba(99,102,241,0.1);">
                <h6 class="section-title border-0 mb-4 justify-content-center text-center"><i data-feather="bar-chart-2" style="color: var(--secondary);"></i> Discrepancy Breakdown</h6>
                
                <div class="row g-3 flex-grow-1 align-items-stretch">
                    <div class="col-md-4">
                        <div class="delta-glass">
                            <div class="delta-title">Ledger Record</div>
                            <div class="delta-val text-muted">{{ $adjustment->recorded_quantity }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="delta-glass" style="background: rgba(255,255,255,0.95); border-color: rgba(99,102,241,0.3); box-shadow: 0 8px 16px rgba(99,102,241,0.1);">
                            <div class="delta-title text-primary">Physical Audit</div>
                            <div class="delta-val text-dark">{{ $adjustment->physical_quantity }}</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        @php
                            $diff = $adjustment->difference_quantity;
                            $diffColor = $diff < 0 ? '#dc2626' : ($diff > 0 ? '#10b981' : '#64748b');
                            $diffBg = $diff < 0 ? 'rgba(239,68,68,0.1)' : ($diff > 0 ? 'rgba(16,185,129,0.1)' : 'rgba(100,116,139,0.1)');
                        @endphp
                        <div class="delta-glass" style="background: {{ $diffBg }}; border-color: {{ $diffColor }}40;">
                            <div class="delta-title" style="color: {{ $diffColor }};">Ledger Delta</div>
                            <div class="delta-val" style="color: {{ $diffColor }};">
                                {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
