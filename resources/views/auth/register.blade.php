@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center py-4">
    <div class="glass-panel d-flex overflow-hidden" style="max-width: 900px; width: 100%; min-height: 600px; border-radius: 24px; box-shadow: 0 24px 48px rgba(0,0,0,0.12);">
        
        <!-- Interactive Form Side -->
        <div class="p-5 d-flex flex-column justify-content-center" style="flex: 1; min-width: 400px; background: rgba(255, 255, 255, 0.75);">
            <div style="max-width: 380px; width: 100%; margin: 0 auto;">
                <div class="text-center d-lg-none mb-4">
                    <i data-feather="hexagon" style="width: 40px; height: 40px; color: var(--primary);"></i>
                </div>
                
                <h3 class="mb-2" style="font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 2rem;">Get Started</h3>
                <p class="text-muted mb-4" style="font-size: 0.95rem;">Join CoreInventory and organize your warehouses.</p>
                
                <form method="POST" action="{{ route('register.post') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label mb-1" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px;">Full Name</label>
                        <div class="position-relative">
                            <i data-feather="user" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <input type="text" name="name" class="form-control" style="padding-left: 42px; height: 44px; background: rgba(255,255,255,0.9);" value="{{ old('name') }}" required autofocus placeholder="John Doe">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label mb-1" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px;">Email Address</label>
                        <div class="position-relative">
                            <i data-feather="mail" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <input type="email" name="email" class="form-control" style="padding-left: 42px; height: 44px; background: rgba(255,255,255,0.9);" value="{{ old('email') }}" required placeholder="you@example.com">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label mb-1" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px;">Password</label>
                            <div class="position-relative">
                                <i data-feather="lock" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="password" name="password" class="form-control" style="padding-left: 42px; height: 44px; background: rgba(255,255,255,0.9);" required placeholder="••••••••">
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label mb-1" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px;">Confirm</label>
                            <div class="position-relative">
                                <i data-feather="check-circle" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                                <input type="password" name="password_confirmation" class="form-control" style="padding-left: 42px; height: 44px; background: rgba(255,255,255,0.9);" required placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-4" style="height: 52px; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        Create Account <i data-feather="user-plus" style="width: 18px;"></i>
                    </button>
                    
                    <div class="text-center pt-2">
                        <span style="font-size: 0.95rem; color: var(--text-muted);">Already have an account? 
                            <a href="{{ route('login') }}" style="color: var(--primary); font-weight: 600; text-decoration: none; border-bottom: 2px solid transparent; transition: all 0.2s;" onmouseover="this.style.borderBottomColor='var(--primary)'" onmouseout="this.style.borderBottomColor='transparent'">Sign in</a>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Animated Brand Side (Reversed for visual balance on register) -->
        <div class="d-none d-lg-flex flex-column justify-content-center align-items-center p-5" style="flex: 1.2; background: linear-gradient(135deg, var(--accent), var(--primary)); color: white; position: relative; overflow: hidden;">
            <div style="position: absolute; top: -50%; right: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 60%); animation: slowSpinReverse 25s linear infinite;"></div>
            <div style="position: relative; z-index: 10; text-align: center;">
                <div style="background: rgba(255,255,255,0.2); padding: 20px; border-radius: 20px; display: inline-block; margin-bottom: 24px; backdrop-filter: blur(10px); box-shadow: 0 8px 32px rgba(0,0,0,0.1);">
                    <i data-feather="activity" style="width: 56px; height: 56px; stroke-width: 1.5; color: white;"></i>
                </div>
                <h2 style="font-family: 'Outfit'; font-weight: 700; font-size: 2.8rem; color: #fff; line-height: 1.1; margin-bottom: 16px;">Take Control</h2>
                <p style="font-size: 1.15rem; opacity: 0.95; font-weight: 300; max-width: 280px; margin: 0 auto; line-height: 1.6;">Build scalable warehouse infrastructure in minutes, not days.</p>
            </div>
            <style>
                @keyframes slowSpinReverse { from { transform: rotate(360deg); } to { transform: rotate(0deg); } }
            </style>
        </div>

    </div>
</div>
@endsection
