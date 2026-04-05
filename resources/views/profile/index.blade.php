@extends('layouts.app')

@push('styles')
<style>
    .page-header { background: var(--bg-surface); backdrop-filter: blur(16px); border: var(--glass-border); border-radius: var(--radius-lg); padding: 24px 32px; box-shadow: var(--glass-shadow); margin-bottom: 24px; }
    [data-theme="dark"] .profile-divider { border-color: rgba(255,255,255,0.1) !important; }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">My Profile</h2>
            <p class="text-muted m-0 mt-1" style="font-size: 1rem;">Manage your account settings and personal information.</p>
        </div>
    </div>

    <div class="glass-panel p-4">
        <div class="row">
            <div class="col-md-3 text-center border-end profile-divider">
                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.8rem; background: rgba(99,102,241,0.15); color: var(--primary); font-family: 'Outfit'; font-weight: 700; border: 1px solid rgba(99,102,241,0.3);">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <h5 class="fw-bold mb-1" style="color: var(--text-main); font-family: 'Outfit';">{{ auth()->user()->name }}</h5>
                <p class="text-muted small mb-0">{{ ucfirst(auth()->user()->role) }}</p>
            </div>
            <div class="col-md-9 ps-4">
                <h6 class="fw-bold mb-4 border-bottom pb-2 profile-divider" style="color: var(--text-main); font-family: 'Outfit';">Account Details</h6>
                
                <div class="row mb-4 align-items-center">
                    <div class="col-sm-3 text-muted fw-bold" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Full Name</div>
                    <div class="col-sm-9" style="color: var(--text-main); font-weight: 600; font-size: 1.1rem;">{{ auth()->user()->name }}</div>
                </div>
                
                <div class="row mb-4 align-items-center">
                    <div class="col-sm-3 text-muted fw-bold" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Email Address</div>
                    <div class="col-sm-9" style="color: var(--text-main); font-weight: 500;">
                        <i data-feather="mail" style="width: 16px; margin-right: 6px; color: var(--text-muted);"></i>
                        {{ auth()->user()->email }}
                    </div>
                </div>
                
                <div class="row mb-3 align-items-center">
                    <div class="col-sm-3 text-muted fw-bold" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">Role Security</div>
                    <div class="col-sm-9">
                        <span class="role-badge role-{{ auth()->user()->role }}">
                            @if(auth()->user()->isAdmin()) <i data-feather="shield" style="width: 12px;"></i> Administrator
                            @elseif(auth()->user()->isManagerOrAbove()) <i data-feather="briefcase" style="width: 12px;"></i> Manager
                            @else <i data-feather="user" style="width: 12px;"></i> Staff
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
