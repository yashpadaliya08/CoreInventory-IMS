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
            <a href="{{ route('products.show', $product) }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;"><i data-feather="arrow-left" style="width: 14px;"></i> Back to Profile</a>
            <h2 class="m-0 mt-2" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Modify Master Profile</h2>
        </div>
    </div>

    <form action="{{ route('products.update', $product) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row g-4 d-flex justify-content-center">
            <div class="col-lg-8">
                <div class="glass-form-card">
                    <h6 class="section-title"><i data-feather="edit-2" style="color: var(--primary);"></i> Core Product Telemetry</h6>
                    
                    <div class="row g-4">
                        <div class="col-md-7">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Product Name <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i data-feather="box" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="text" name="name" class="form-control form-glass" style="padding-left: 42px;" value="{{ old('name', $product->name) }}" required>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">SKU Code <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i data-feather="hash" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="text" name="sku" class="form-control form-glass text-uppercase" style="padding-left: 42px; font-family: 'Outfit'; font-weight: 700;" value="{{ old('sku', $product->sku) }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Category Taxonomy</label>
                            <div class="position-relative">
                                <i data-feather="tag" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="text" name="category" class="form-control form-glass" style="padding-left: 42px;" value="{{ old('category', $product->category) }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Measurement Unit <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <i data-feather="rulers" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <select name="unit_of_measure" class="form-select form-glass" style="padding-left: 42px;" required>
                                    <option value="Units" {{ old('unit_of_measure', $product->unit_of_measure) == 'Units' ? 'selected' : '' }}>Units (pcs)</option>
                                    <option value="Kg" {{ old('unit_of_measure', $product->unit_of_measure) == 'Kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                    <option value="Liters" {{ old('unit_of_measure', $product->unit_of_measure) == 'Liters' ? 'selected' : '' }}>Liters (L)</option>
                                    <option value="Boxes" {{ old('unit_of_measure', $product->unit_of_measure) == 'Boxes' ? 'selected' : '' }}>Boxes</option>
                                    <option value="pcs" {{ old('unit_of_measure', $product->unit_of_measure) == 'pcs' ? 'selected' : '' }}>Pieces (pcs)</option>
                                    <option value="kg" {{ old('unit_of_measure', $product->unit_of_measure) == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                    <option value="liters" {{ old('unit_of_measure', $product->unit_of_measure) == 'liters' ? 'selected' : '' }}>Liters (L)</option>
                                    <option value="boxes" {{ old('unit_of_measure', $product->unit_of_measure) == 'boxes' ? 'selected' : '' }}>Boxes</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6 mt-4 pt-3 border-top w-100">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Reorder Restock Level <span class="text-danger">*</span></label>
                            <p class="text-muted small mb-2">Adjust the minimum inventory threshold alarm logic.</p>
                            <div class="position-relative" style="max-width: 300px;">
                                <i data-feather="alert-triangle" style="position: absolute; top: 18px; left: 14px; color: var(--text-main); width: 22px;"></i>
                                <input type="number" name="reorder_level" class="form-control form-glass" style="padding-left: 46px; height: 60px; font-size: 1.5rem; font-family: 'Outfit'; font-weight: 700; color: var(--primary);" value="{{ old('reorder_level', $product->reorder_level) }}" required min="0">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-5">
                        <button type="submit" class="btn btn-primary d-flex justify-content-center align-items-center gap-2 w-100" style="height: 54px; font-size: 1.1rem; border-radius: 12px; box-shadow: 0 8px 20px rgba(99,102,241,0.3);">
                            <i data-feather="upload-cloud"></i> Push Network Updates
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
