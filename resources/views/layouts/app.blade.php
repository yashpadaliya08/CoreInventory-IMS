<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoreInventory IMS</title>
    <!-- Keep Bootstrap for structure compatibility during transition -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Feather Icons for stunning UI elements -->
    <script src="https://unpkg.com/feather-icons"></script>
    <!-- Our bespoke premium styling -->
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
    @stack('styles')
</head>
<body>
    <div class="app-container">
        
        <!-- Only show sidebar if user is authenticated and not on login/register -->
        @if(auth()->check() && !request()->is('login') && !request()->is('register') && !request()->is('otp/*'))
        <!-- Dynamic Sidebar Navigation -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="sidebar-brand">
                    <i data-feather="box" style="width: 28px; height: 28px;"></i>
                    CoreInventory
                </a>
            </div>
            
            <div class="sidebar-nav">
                <a href="{{ route('dashboard') }}" class="nav-item">
                    <i data-feather="grid" class="nav-icon"></i> Dashboard
                </a>
                
                <div class="nav-section-title">Master Data</div>
                <a href="{{ route('products.index') }}" class="nav-item">
                    <i data-feather="package" class="nav-icon"></i> Products
                </a>
                
                <div class="nav-section-title">Procurement</div>
                <a href="{{ route('vendors.index') }}" class="nav-item">
                    <i data-feather="briefcase" class="nav-icon"></i> Vendors
                </a>
                <a href="{{ route('purchase-orders.index') }}" class="nav-item">
                    <i data-feather="file-plus" class="nav-icon"></i> Purchase Orders
                </a>

                <div class="nav-section-title">Movements</div>
                <a href="{{ route('receipts.index') }}" class="nav-item">
                    <i data-feather="arrow-down-circle" class="nav-icon"></i> Receipts
                </a>
                <a href="{{ route('deliveries.index') }}" class="nav-item">
                    <i data-feather="arrow-up-right" class="nav-icon"></i> Deliveries
                </a>
                <a href="{{ route('transfers.index') }}" class="nav-item">
                    <i data-feather="repeat" class="nav-icon"></i> Transfers
                </a>
                <a href="{{ route('adjustments.index') }}" class="nav-item">
                    <i data-feather="sliders" class="nav-icon"></i> Adjustments
                </a>
                
                <div class="nav-section-title">Reporting & Alerts</div>
                <a href="{{ route('alerts.index') ?? '#' }}" class="nav-item">
                    <i data-feather="bell" class="nav-icon"></i> Low Stock Alerts
                    @if(isset($globalLowStockCount) && $globalLowStockCount > 0)
                        <span class="badge bg-danger ms-auto rounded-pill">{{ $globalLowStockCount }}</span>
                    @endif
                </a>
                <a href="{{ route('ledger.index') }}" class="nav-item">
                    <i data-feather="activity" class="nav-icon"></i> Move History
                </a>
                <a href="{{ route('activity-log.index') }}" class="nav-item">
                    <i data-feather="eye" class="nav-icon"></i> Audit Log
                </a>
                
                @if(auth()->user() && auth()->user()->isAdmin())
                    <div class="nav-section-title">Admin</div>
                    <a href="{{ route('settings.index') }}" class="nav-item">
                        <i data-feather="settings" class="nav-icon"></i> Settings
                    </a>
                    <a href="{{ route('users.index') }}" class="nav-item">
                        <i data-feather="users" class="nav-icon"></i> Users
                    </a>
                @endif
            </div>

            <div class="sidebar-footer" style="flex-direction: column; gap: 16px;">
                <a href="{{ route('profile.index') }}" class="user-profile-preview w-100">
                    <div class="user-avatar">
                        {{ substr(auth()->user()->name ?? 'G', 0, 1) }}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div class="fw-semibold text-truncate" style="font-size: 0.9rem; color: var(--text-main); margin-bottom: 4px;">
                            {{ auth()->user()->name ?? 'Guest User' }}
                        </div>
                        @if(auth()->check())
                            <div class="role-badge role-{{ auth()->user()->role }}" style="font-size: 0.6rem; padding: 2px 8px;">
                                {{ ucfirst(auth()->user()->role) }}
                            </div>
                        @endif
                    </div>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i data-feather="log-out" class="nav-icon" style="opacity: 1; stroke: #ef4444;"></i> Logout
                    </button>
                </form>
            </div>
        </nav>
        <!-- Main Workspace -->
        <main class="main-content">
            <div class="fade-in">
                <!-- Notifications/Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 glass-panel mb-4" role="alert" style="background: rgba(209, 250, 229, 0.9);">
                        <i data-feather="check-circle" class="me-2" style="width: 18px; height: 18px; stroke: #059669;"></i>
                        <span style="color: #065f46; font-weight: 500;">{{ session('success') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error') || $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 glass-panel mb-4" role="alert" style="background: rgba(254, 226, 226, 0.9);">
                        <i data-feather="alert-circle" class="me-2" style="width: 18px; height: 18px; stroke: #dc2626;"></i>
                        <span style="color: #991b1b; font-weight: 500;">{{ session('error') ?? $errors->first() }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
        
        @else
        <!-- Guest Content (Login/Register) -->
        <main class="w-100 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="fade-in w-100">
                @if(session('status'))
                    <div class="alert alert-success alert-dismissible fade show border-0 mx-auto mb-3" role="alert" style="max-width: 520px; background: rgba(209, 250, 229, 0.95); border-radius: 12px;">
                        <i data-feather="check-circle" class="me-2" style="width: 18px; height: 18px; stroke: #059669;"></i>
                        <span style="color: #065f46; font-weight: 500;">{{ session('status') }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if(session('error') || $errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 mx-auto mb-3" role="alert" style="max-width: 520px; background: rgba(254, 226, 226, 0.95); border-radius: 12px;">
                        <i data-feather="alert-circle" class="me-2" style="width: 18px; height: 18px; stroke: #dc2626;"></i>
                        <span style="color: #991b1b; font-weight: 500;">{{ session('error') ?? $errors->first() }}</span>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
        @endif
    </div>

    <!-- Bootstrap & Tooling Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmxc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        // Start Feather Icons
        feather.replace();

        // Active State Logic for Sidebar
        document.addEventListener('DOMContentLoaded', () => {
            const currentPath = window.location.pathname;
            document.querySelectorAll('.sidebar-nav .nav-item').forEach(link => {
                const linkPath = new URL(link.getAttribute('href'), window.location.origin).pathname;
                
                // Exact match for dashboard
                if (linkPath === '/' && (currentPath === '/' || currentPath === '/dashboard')) {
                    link.classList.add('active');
                } 
                // Prefix match for modules (e.g. /products/create highlights /products)
                else if (linkPath !== '/' && currentPath.startsWith(linkPath)) {
                    link.classList.add('active');
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
