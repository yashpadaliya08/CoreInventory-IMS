@extends('layouts.app')

@push('styles')
<style>
    .kpi-card {
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        border-radius: var(--radius-xl) !important;
        animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        opacity: 0;
        z-index: 1;
        background: rgba(255, 255, 255, 0.65) !important;
    }
    
    .kpi-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08) !important;
        background: rgba(255, 255, 255, 0.85) !important;
        z-index: 10;
    }
    
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--secondary));
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .kpi-card:hover::before {
        opacity: 1;
    }
    
    .icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        position: relative;
        transition: transform 0.3s ease;
    }
    
    .kpi-card:hover .icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }
    
    .icon-wrapper svg {
        width: 28px;
        height: 28px;
        stroke-width: 2;
    }
    
    .kpi-label {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 6px;
    }
    
    .kpi-value {
        font-family: 'Outfit', sans-serif;
        font-size: 3.2rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1;
        letter-spacing: -1px;
    }
    
    .kpi-alert {
        background: rgba(254, 226, 226, 0.7) !important;
        border: 1px solid rgba(239, 68, 68, 0.2) !important;
    }
    
    .kpi-alert:hover {
        background: rgba(254, 226, 226, 0.95) !important;
    }
    
    .kpi-alert .kpi-value { color: #b91c1c; }
    
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
    .delay-5 { animation-delay: 0.5s; }
    
    /* Sleek UI Header */
    .page-header {
        background: rgba(255, 255, 255, 0.5);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255,255,255,0.8);
        border-radius: var(--radius-lg);
        padding: 24px 32px;
        margin-bottom: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    
    @media (max-width: 768px) {
        .page-header { flex-direction: column; align-items: flex-start; gap: 16px; }
        .page-header form { width: 100%; }
        .page-header select { width: 100%; }
    }
</style>
@endpush

@section('content')
<!-- Filter Header -->
<div class="page-header" style="animation: slideUp 0.5s forwards;">
    <div>
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="text-muted" style="font-weight: 600; font-size: 0.9rem;">Welcome back, {{ auth()->user()->name }}</span>
            <span class="role-badge role-{{ auth()->user()->role }}">
                @if(auth()->user()->isAdmin()) <i data-feather="shield" style="width: 12px;"></i> Administrator
                @elseif(auth()->user()->isManagerOrAbove()) <i data-feather="briefcase" style="width: 12px;"></i> Manager
                @else <i data-feather="user" style="width: 12px;"></i> Staff (Read-Only)
                @endif
            </span>
        </div>
        <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Command Center</h2>
        <p class="text-muted m-0 mt-1" style="font-size: 1rem;">Real-time overview of your warehouse operations.</p>
    </div>
    <form method="GET" action="{{ route('dashboard') }}" class="d-flex align-items-center">
        <label for="locationFilter" class="me-3 text-muted fw-bold d-none d-md-block" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px;">Filter View</label>
        <div class="position-relative">
            <i data-feather="map-pin" style="position: absolute; top: 11px; left: 14px; color: var(--primary); width: 18px;"></i>
            <select name="location_id" id="locationFilter" class="form-select" style="padding-left: 44px; padding-right: 36px; height: 42px; border-radius: 12px; min-width: 240px; font-weight: 600; font-size: 0.95rem; background-color: rgba(255,255,255,0.9); box-shadow: 0 2px 8px rgba(0,0,0,0.02); cursor: pointer;" onchange="this.form.submit()">
                <option value="">Global (All Locations)</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ isset($locationId) && $locationId == $loc->id ? 'selected' : '' }}>
                        {{ $loc->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
</div>

<!-- KPI Grid -->
<div class="row g-4 mb-4">
    <!-- Total Products -->
    <div class="col-xl-4 col-md-6">
        <div class="card kpi-card delay-1 p-4">
            <div class="icon-wrapper" style="background: rgba(99, 102, 241, 0.1); color: var(--primary);">
                <i data-feather="package"></i>
            </div>
            <div class="kpi-label">Active Products</div>
            <div class="kpi-value">{{ $totalProducts }}</div>
        </div>
    </div>
    
    <!-- Low Stock (Dynamic Alert State) -->
    <div class="col-xl-4 col-md-6">
        <div class="card kpi-card delay-2 p-4 {{ $lowStockCount > 0 ? 'kpi-alert' : '' }}">
            <div class="icon-wrapper" style="background: {{ $lowStockCount > 0 ? 'rgba(239, 68, 68, 0.15)' : 'rgba(34, 197, 94, 0.1)' }}; color: {{ $lowStockCount > 0 ? '#dc2626' : '#16a34a' }};">
                <i data-feather="{{ $lowStockCount > 0 ? 'alert-triangle' : 'check-circle' }}"></i>
            </div>
            <div class="kpi-label" style="color: {{ $lowStockCount > 0 ? '#b91c1c' : 'var(--text-muted)' }};">Low Stock Alerts</div>
            <div class="kpi-value">{{ $lowStockCount }}</div>
        </div>
    </div>
    
    <!-- Pending Receipts -->
    <div class="col-xl-4 col-md-4">
        <div class="card kpi-card delay-3 p-4">
            <div class="icon-wrapper" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                <i data-feather="arrow-down-circle"></i>
            </div>
            <div class="kpi-label">Awaiting Receipt</div>
            <div class="kpi-value">{{ $pendingReceipts }}</div>
        </div>
    </div>
    
    <!-- Pending Deliveries -->
    <div class="col-xl-6 col-md-4">
        <div class="card kpi-card delay-4 p-4">
            <div class="icon-wrapper" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <i data-feather="arrow-up-right"></i>
            </div>
            <div class="kpi-label">Pending Deliveries</div>
            <div class="kpi-value">{{ $pendingDeliveries }}</div>
        </div>
    </div>
    
    <!-- Scheduled Transfers -->
    <div class="col-xl-6 col-md-4">
        <div class="card kpi-card delay-5 p-4">
            <div class="icon-wrapper" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                <i data-feather="repeat"></i>
            </div>
            <div class="kpi-label">Scheduled Transfers</div>
            <div class="kpi-value">{{ $scheduledTransfers }}</div>
        </div>
    </div>
</div>
@endsection
