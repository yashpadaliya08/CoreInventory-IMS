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
            <a href="{{ route('products.index') }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;"><i data-feather="arrow-left" style="width: 14px;"></i> Back to Database</a>
            <h2 class="m-0 mt-2" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">New Product Registration</h2>
        </div>
    </div>

    <form action="{{ route('products.store') }}" method="POST">
        @csrf
        <div class="row g-4 d-flex justify-content-center">
            <div class="col-lg-8">
                <div class="glass-form-card">
                    <h6 class="section-title"><i data-feather="database" style="color: var(--primary);"></i> Master Data Entry</h6>
                    
                    <div class="row g-4">
                        <div class="col-md-7">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Product Name <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i data-feather="box" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="text" name="name" class="form-control form-glass" style="padding-left: 42px;" required placeholder="e.g. Premium Widget">
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">SKU Code <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i data-feather="hash" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="text" name="sku" class="form-control form-glass text-uppercase" style="padding-left: 42px; font-family: 'Outfit'; font-weight: 700;" required placeholder="e.g. WDG-001">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Category Taxonomy <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i data-feather="tag" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <select name="category" class="form-select form-glass" style="padding-left: 42px;" required>
                                    <option value="">Select Category...</option>
                                    @foreach($categories as $catName)
                                        <option value="{{ $catName }}">{{ $catName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Measurement Unit <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i data-feather="rulers" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <select name="unit_of_measure" class="form-select form-glass" style="padding-left: 42px;" required>
                                    <option value="Units">Units (pcs)</option>
                                    <option value="Kg">Kilograms (kg)</option>
                                    <option value="Liters">Liters (L)</option>
                                    <option value="Boxes">Boxes</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mt-5">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Reorder Threshold <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i data-feather="alert-triangle" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="number" name="reorder_level" class="form-control form-glass" style="padding-left: 42px; font-family: 'Outfit'; font-weight: 700;" min="0" value="10" required>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">System warns when stock drops below this value.</small>
                        </div>

                        <div class="col-md-6 mt-5 border-start">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Initial Stock (Optional)</label>
                            <div class="position-relative">
                                <i data-feather="database" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="number" name="initial_stock" class="form-control form-glass" style="padding-left: 42px; font-family: 'Outfit'; font-weight: 700;" min="0" value="0">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Creates an automatic initial inventory adjustment.</small>
                        </div>

                        <div class="col-12 mt-4 pt-3 border-top">
                            <h6 class="mb-3" style="font-family: 'Outfit'; font-weight: 700; color: var(--text-muted); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 1px;">💰 Pricing (Optional)</h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Unit Cost (₹)</label>
                            <div class="position-relative">
                                <i data-feather="trending-down" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="number" step="0.01" name="unit_cost" class="form-control form-glass" style="padding-left: 42px; font-family: 'Outfit'; font-weight: 700;" min="0" value="{{ old('unit_cost', 0) }}" placeholder="0.00">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">What you pay to purchase this product.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Selling Price (₹)</label>
                            <div class="position-relative">
                                <i data-feather="trending-up" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="number" step="0.01" name="selling_price" class="form-control form-glass" style="padding-left: 42px; font-family: 'Outfit'; font-weight: 700;" min="0" value="{{ old('selling_price', 0) }}" placeholder="0.00">
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">What you charge your customers.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-5">
                        <button type="submit" class="btn btn-primary d-flex justify-content-center align-items-center gap-2 w-100" style="height: 54px; font-size: 1.1rem; border-radius: 12px; box-shadow: 0 8px 20px rgba(99,102,241,0.3);">
                            <i data-feather="save"></i> Publish Product Profile
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
