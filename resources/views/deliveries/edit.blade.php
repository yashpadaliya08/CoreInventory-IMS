@extends('layouts.app')

@push('styles')
<style>
    .page-header { margin-bottom: 32px; }
    .glass-form-card { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.8); border-radius: var(--radius-lg); padding: 32px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 24px; }
    .form-glass { background: rgba(255, 255, 255, 0.9); border: 1px solid rgba(0,0,0,0.05); border-radius: 8px; padding: 12px 16px; height: 48px; transition: all 0.2s; }
    .form-glass:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    .section-title { font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.25rem; display: flex; align-items: center; gap: 8px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid rgba(0,0,0,0.03); }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('deliveries.show', $delivery) }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;"><i data-feather="arrow-left" style="width: 14px;"></i> Back to Document</a>
            <h2 class="m-0 mt-2" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Edit Delivery {{ $delivery->reference_no }}</h2>
        </div>
    </div>

    <form action="{{ route('deliveries.update', $delivery) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4 d-flex justify-content-center">
            <div class="col-lg-6">
                <div class="glass-form-card">
                    <h6 class="section-title"><i data-feather="edit-2" style="color: var(--primary);"></i> Logistics Details Modification</h6>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Customer Identity</label>
                        <div class="position-relative">
                            <i data-feather="users" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <input type="text" name="customer_name" class="form-control form-glass" style="padding-left: 42px;" value="{{ old('customer_name', $delivery->customer_name) }}">
                        </div>
                    </div>
                    
                    <div class="mb-5">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Scheduled Ship Date</label>
                        <div class="position-relative">
                            <i data-feather="calendar" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <input type="date" name="scheduled_date" class="form-control form-glass" style="padding-left: 42px;" value="{{ old('scheduled_date', $delivery->scheduled_date ? \Carbon\Carbon::parse($delivery->scheduled_date)->format('Y-m-d') : '') }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary d-flex justify-content-center align-items-center gap-2 w-100" style="height: 54px; font-size: 1.1rem; border-radius: 12px; box-shadow: 0 8px 20px rgba(99,102,241,0.3);">
                            <i data-feather="save"></i> Update Document Info
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
