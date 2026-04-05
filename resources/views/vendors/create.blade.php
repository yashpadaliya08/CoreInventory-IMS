@extends('layouts.app')

@push('styles')
<style>
    .page-header { margin-bottom: 32px; }
    .glass-form-card { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.8); border-radius: var(--radius-lg); padding: 32px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); margin-bottom: 24px; }
    .form-glass { background: rgba(255, 255, 255, 0.9); border: 1px solid rgba(0,0,0,0.05); border-radius: 8px; padding: 12px 16px; height: 48px; transition: all 0.2s; }
    .form-glass:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    .section-title { font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.25rem; display: flex; align-items: center; gap: 8px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid rgba(0,0,0,0.03); }
    textarea.form-glass { height: auto; min-height: 100px; }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('vendors.index') }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;"><i data-feather="arrow-left" style="width: 14px;"></i> Back to Vendors</a>
            <h2 class="m-0 mt-2" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Register New Vendor</h2>
        </div>
    </div>

    <form action="{{ route('vendors.store') }}" method="POST">
        @csrf
        <div class="row g-4 d-flex justify-content-center">
            <div class="col-lg-8">
                <div class="glass-form-card">
                    <h6 class="section-title"><i data-feather="briefcase" style="color: var(--primary);"></i> Vendor Information</h6>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Company Name *</label>
                            <div class="position-relative">
                                <i data-feather="home" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="text" name="name" class="form-control form-glass" style="padding-left: 42px;" placeholder="e.g. Acme Supplies Ltd." value="{{ old('name') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Contact Person</label>
                            <div class="position-relative">
                                <i data-feather="user" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="text" name="contact_person" class="form-control form-glass" style="padding-left: 42px;" placeholder="e.g. John Smith" value="{{ old('contact_person') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mt-0">
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Email Address</label>
                            <div class="position-relative">
                                <i data-feather="mail" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="email" name="email" class="form-control form-glass" style="padding-left: 42px;" placeholder="vendor@example.com" value="{{ old('email') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Phone Number</label>
                            <div class="position-relative">
                                <i data-feather="phone" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="text" name="phone" class="form-control form-glass" style="padding-left: 42px;" placeholder="+91 98765 43210" value="{{ old('phone') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Address</label>
                        <div class="position-relative">
                            <i data-feather="map-pin" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <textarea name="address" class="form-control form-glass" style="padding-left: 42px; padding-top: 14px;" rows="3" placeholder="Full business address...">{{ old('address') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-5">
                        <button type="submit" class="btn btn-primary d-flex justify-content-center align-items-center gap-2" style="height: 54px; padding: 0 40px; font-size: 1.1rem; border-radius: 12px; box-shadow: 0 8px 20px rgba(99,102,241,0.3);">
                            <i data-feather="save"></i> Save Vendor
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
