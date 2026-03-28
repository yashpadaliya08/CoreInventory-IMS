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
    .qty-badge { background: rgba(0, 0, 0, 0.8); color: white; font-family: 'Outfit'; font-weight: 700; padding: 6px 14px; border-radius: 8px; font-size: 1.15rem; }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <a href="{{ route('transfers.index') }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;"><i data-feather="arrow-left" style="width: 14px;"></i> Back to Deliveries</a>
            <div class="d-flex align-items-center gap-3 mt-2">
                <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Transfer {{ $transfer->reference_no }}</h2>
                <span class="status-badge badge-{{ $transfer->status }}">{{ $transfer->status }}</span>
            </div>
        </div>
        
        <div class="d-flex flex-wrap gap-2">
            @if($transfer->status !== 'Done')
                @if(auth()->user() && auth()->user()->isManagerOrAbove())
                <form action="{{ route('transfers.validate', $transfer) }}" method="POST" onsubmit="return confirm('Execute transfer? Stock will be shifted physically.');" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 fw-bold" style="height: 48px; border-radius: 10px; box-shadow: 0 4px 12px rgba(99,102,241,0.3);">
                        <i data-feather="check-circle" style="width: 18px;"></i> Execute Transfer Node
                    </button>
                </form>
                @endif
            @endif
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="glass-card h-100">
                <h6 class="section-title"><i data-feather="map" style="color: var(--primary);"></i> Logistics Details</h6>
                
                <div class="mb-4">
                    <div class="info-label">Source Node (Origin)</div>
                    <div class="info-value d-flex align-items-center gap-2" style="background: rgba(239, 68, 68, 0.05); border-color: rgba(239, 68, 68, 0.2);">
                        <i data-feather="minus-circle" style="width: 16px; color: #ef4444;"></i>
                        {{ $transfer->fromLocation->name ?? 'N/A' }}
                    </div>
                </div>
                
                <div class="mb-2">
                    <div class="info-label">Destination Node (Target)</div>
                    <div class="info-value d-flex align-items-center gap-2" style="background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.2);">
                        <i data-feather="plus-circle" style="width: 16px; color: #10b981;"></i>
                        {{ $transfer->toLocation->name ?? 'N/A' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="glass-panel overflow-hidden h-100">
                <div class="p-4 border-bottom" style="background: rgba(255,255,255,0.4);">
                    <h6 class="m-0" style="font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.15rem; display: flex; align-items: center; gap: 8px;">
                        <i data-feather="repeat" style="color: var(--secondary); width: 20px;"></i> Transit Items List
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead style="background: rgba(0,0,0,0.02);">
                            <tr>
                                <th class="ps-4 border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Product Identity</th>
                                <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">SKU Number</th>
                                <th class="border-0 text-end pe-4" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Transit Volume</th>
                            </tr>
                        </thead>
                        <tbody style="border-top: 2px solid rgba(0,0,0,0.05);">
                            @foreach($transfer->transferItems as $item)
                            <tr>
                                <td class="ps-4 fw-bold" style="color: var(--text-main); font-size: 1.05rem;">{{ $item->product->name }}</td>
                                <td><span style="font-family: 'Outfit'; font-weight: 600; padding: 4px 8px; background: rgba(0,0,0,0.04); border-radius: 6px; color: var(--text-muted);">{{ $item->product->sku }}</span></td>
                                <td class="text-end pe-4">
                                    <span class="qty-badge">{{ $item->quantity }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
