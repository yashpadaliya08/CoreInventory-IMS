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
    
    /* Charts */
    .chart-card {
        background: rgba(255, 255, 255, 0.65);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255,255,255,0.8);
        border-radius: var(--radius-xl);
        padding: 24px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        opacity: 0;
    }
    
    .chart-title {
        font-family: inherit;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 20px;
    }
    
    .stat-row {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 16px 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .stat-row:last-child {
        border-bottom: none;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .stat-icon svg {
        width: 22px;
        height: 22px;
    }
    .delay-6 { animation-delay: 0.6s; }
    .delay-7 { animation-delay: 0.7s; }
    .delay-8 { animation-delay: 0.8s; }
    .delay-9 { animation-delay: 0.9s; }
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

<!-- KPI Grid Row 1: Valuations -->
<div class="row g-4 mb-4">
    <!-- Total Valuation -->
    <div class="col-xl-6 col-md-6">
        <div class="card kpi-card delay-1 p-4" style="background: linear-gradient(135deg, rgba(99,102,241,0.1) 0%, rgba(255,255,255,0.8) 100%) !important; border: 1px solid rgba(99,102,241,0.3);">
            <div class="icon-wrapper" style="background: var(--primary); color: white; box-shadow: 0 8px 20px rgba(99,102,241,0.3);">
                <i data-feather="dollar-sign"></i>
            </div>
            <div class="kpi-label">Total Warehouse Valuation</div>
            <div class="kpi-value" style="color: var(--primary);">₹{{ number_format($totalValuation, 2) }}</div>
        </div>
    </div>
    
    <!-- Pending PO Value -->
    <div class="col-xl-6 col-md-6">
        <div class="card kpi-card delay-2 p-4" style="background: linear-gradient(135deg, rgba(236,72,153,0.1) 0%, rgba(255,255,255,0.8) 100%) !important; border: 1px solid rgba(236,72,153,0.3);">
            <div class="icon-wrapper" style="background: var(--secondary); color: white; box-shadow: 0 8px 20px rgba(236,72,153,0.3);">
                <i data-feather="trending-up"></i>
            </div>
            <div class="kpi-label">Value of Pending Inbound POs</div>
            <div class="kpi-value" style="color: var(--secondary);">₹{{ number_format($pendingPoValue, 2) }}</div>
        </div>
    </div>
</div>

<!-- KPI Grid Row 2: Operational -->
<div class="row g-4 mb-4">
    <!-- Total Products -->
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card delay-3 p-4">
            <div class="icon-wrapper" style="background: rgba(99, 102, 241, 0.1); color: var(--primary);">
                <i data-feather="package"></i>
            </div>
            <div class="kpi-label">Active SKUs</div>
            <div class="kpi-value">{{ $totalProducts }}</div>
        </div>
    </div>
    
    <!-- Low Stock (Dynamic Alert State) -->
    <div class="col-xl-3 col-md-6">
        <div class="card kpi-card delay-4 p-4 {{ $lowStockCount > 0 ? 'kpi-alert' : '' }}">
            <div class="icon-wrapper" style="background: {{ $lowStockCount > 0 ? 'rgba(239, 68, 68, 0.15)' : 'rgba(34, 197, 94, 0.1)' }}; color: {{ $lowStockCount > 0 ? '#dc2626' : '#16a34a' }};">
                <i data-feather="{{ $lowStockCount > 0 ? 'alert-triangle' : 'check-circle' }}"></i>
            </div>
            <div class="kpi-label" style="color: {{ $lowStockCount > 0 ? '#b91c1c' : 'var(--text-muted)' }};">Low Stock Alerts</div>
            <div class="kpi-value">{{ $lowStockCount }}</div>
        </div>
    </div>
    
    <!-- Pending Receipts -->
    <div class="col-xl-2 col-md-4">
        <div class="card kpi-card delay-5 p-4">
            <div class="icon-wrapper" style="width: 45px; height: 45px; background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                <i data-feather="arrow-down-circle" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="kpi-label" style="font-size: 0.7rem;">Awaiting Receipt</div>
            <div class="kpi-value" style="font-size: 2.2rem;">{{ $pendingReceipts }}</div>
        </div>
    </div>
    
    <!-- Pending Deliveries -->
    <div class="col-xl-2 col-md-4">
        <div class="card kpi-card delay-6 p-4">
            <div class="icon-wrapper" style="width: 45px; height: 45px; background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                <i data-feather="arrow-up-right" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="kpi-label" style="font-size: 0.7rem;">Pending Delivery</div>
            <div class="kpi-value" style="font-size: 2.2rem;">{{ $pendingDeliveries }}</div>
        </div>
    </div>
    
    <!-- Scheduled Transfers -->
    <div class="col-xl-2 col-md-4">
        <div class="card kpi-card delay-7 p-4">
            <div class="icon-wrapper" style="width: 45px; height: 45px; background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                <i data-feather="repeat" style="width: 20px; height: 20px;"></i>
            </div>
            <div class="kpi-label" style="font-size: 0.7rem;">Transfers</div>
            <div class="kpi-value" style="font-size: 2.2rem;">{{ $scheduledTransfers }}</div>
        </div>
    </div>
</div>

<!-- Charts Section Row 1 -->
<div class="row g-4 mb-4">
    <!-- Trend Chart -->
    <div class="col-xl-8">
        <div class="chart-card delay-6">
            <h3 class="chart-title">Stock Movement — Last 30 Days</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Category Doughnut -->
    <div class="col-xl-4">
        <div class="chart-card delay-7">
            <h3 class="chart-title">Global Wealth by Category (Valuation)</h3>
            <div style="position: relative; height: 280px; width: 100%;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section Row 2 -->
<div class="row g-4 mb-4">
    <!-- Low Stock Bar Chart -->
    <div class="col-xl-8">
        <div class="chart-card delay-8">
            <h3 class="chart-title">Low Stock Monitor</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="stockChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="col-xl-4">
        <div class="chart-card delay-9 h-100">
            <h3 class="chart-title mb-0">Recent Activity Summary</h3>
            
            <div class="stat-row">
                <div class="stat-icon" style="background: rgba(14, 165, 233, 0.1); color: #0ea5e9;">
                    <i data-feather="download"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Receipts Validated</div>
                    <div style="font-size: 1.2rem; font-weight: 700; color: var(--text-main);">{{ number_format($recentActivity['receipts']) }}</div>
                </div>
            </div>
            
            <div class="stat-row">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">
                    <i data-feather="upload"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Deliveries Completed</div>
                    <div style="font-size: 1.2rem; font-weight: 700; color: var(--text-main);">{{ number_format($recentActivity['deliveries']) }}</div>
                </div>
            </div>
            
            <div class="stat-row">
                <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                    <i data-feather="repeat"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Transfers Executed</div>
                    <div style="font-size: 1.2rem; font-weight: 700; color: var(--text-main);">{{ number_format($recentActivity['transfers']) }}</div>
                </div>
            </div>
            
            <div class="stat-row">
                <div class="stat-icon" style="background: rgba(99, 102, 241, 0.1); color: #6366f1;">
                    <i data-feather="sliders"></i>
                </div>
                <div style="flex: 1;">
                    <div style="font-size: 0.8rem; color: var(--text-muted); font-weight: 600;">Adjustments Applied</div>
                    <div style="font-size: 1.2rem; font-weight: 700; color: var(--text-main);">{{ number_format($recentActivity['adjustments']) }}</div>
                </div>
            </div>
            
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
    const trendLabels = @json($trendLabels);
    const trendIn     = @json($trendIn);
    const trendOut    = @json($trendOut);
    const categoryLabels = @json($categoryLabels);
    const categoryCounts = @json($categoryCounts);
    const stockLabels   = @json($stockLabels);
    const stockCurrent  = @json($stockCurrent);
    const stockReorder  = @json($stockReorder);

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.plugins.legend.labels.usePointStyle = true;

    // 1. Trend Line Chart
    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: trendLabels,
            datasets: [
                {
                    label: 'Stock In',
                    data: trendIn,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Stock Out',
                    data: trendOut,
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                x: {
                    grid: { display: false }
                },
                y: {
                    grid: { color: 'rgba(0,0,0,0.05)', borderDash: [5, 5] },
                    beginAtZero: true
                }
            }
        }
    });

    // 2. Category Doughnut Chart
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryCounts,
                backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#0ea5e9'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 20 }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed !== null) {
                                label += new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(context.parsed);
                            }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // 3. Low Stock Bar Chart
    new Chart(document.getElementById('stockChart'), {
        type: 'bar',
        data: {
            labels: stockLabels,
            datasets: [
                {
                    label: 'Current Stock',
                    data: stockCurrent,
                    backgroundColor: stockCurrent.map((val, idx) => val < stockReorder[idx] ? '#ef4444' : '#10b981'),
                    borderRadius: 4
                },
                {
                    label: 'Reorder Level',
                    data: stockReorder,
                    type: 'line',
                    borderColor: '#f59e0b',
                    backgroundColor: '#f59e0b',
                    borderWidth: 2,
                    pointRadius: 4,
                    showLine: false
                }
            ]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: { color: 'rgba(0,0,0,0.05)', borderDash: [5, 5] },
                    beginAtZero: true
                },
                y: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush
