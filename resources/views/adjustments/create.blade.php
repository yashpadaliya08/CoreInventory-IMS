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
            <a href="{{ route('adjustments.index') }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;"><i data-feather="arrow-left" style="width: 14px;"></i> Back to Adjustments</a>
            <h2 class="m-0 mt-2" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">New Inventory Adjustment</h2>
        </div>
    </div>

    <form action="{{ route('adjustments.store') }}" method="POST">
        @csrf
        <div class="row g-4 d-flex justify-content-center">
            <div class="col-lg-8">
                <div class="glass-form-card">
                    <h6 class="section-title"><i data-feather="sliders" style="color: var(--primary);"></i> Discrepancy Registration</h6>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Target Location <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i data-feather="map-pin" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <select name="location_id" class="form-select form-glass" style="padding-left: 42px;" required>
                                    <option value="">Select Location...</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}">{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Product Identity <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i data-feather="box" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <select name="product_id" class="form-select form-glass" style="padding-left: 42px;" required>
                                    <option value="">Select Product...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} (SKU: {{ $product->sku }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-12 mt-4 pt-4 border-top">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Actual Physical Count <span class="text-danger">*</span></label>
                            <p class="text-muted small mb-3">Enter the exact stock quantity counted physically in the warehouse. The system will automatically compute the delta against ledgers.</p>
                            <div class="position-relative">
                                <i data-feather="hash" style="position: absolute; top: 18px; left: 14px; color: var(--text-main); width: 22px;"></i>
                                <input type="number" name="physical_count" class="form-control form-glass" style="padding-left: 46px; height: 60px; font-size: 1.5rem; font-family: 'Outfit'; font-weight: 700; color: var(--primary);" required min="0" placeholder="0">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-5">
                        <button type="submit" class="btn btn-primary d-flex justify-content-center align-items-center gap-2 w-100" style="height: 54px; font-size: 1.1rem; border-radius: 12px; box-shadow: 0 8px 20px rgba(99,102,241,0.3);">
                            <i data-feather="save"></i> Submit Adjustment Form
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
