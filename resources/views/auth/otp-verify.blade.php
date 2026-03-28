@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center py-4">
    <div class="glass-panel overflow-hidden" style="max-width: 520px; width: 100%; border-radius: 24px; box-shadow: 0 24px 48px rgba(0,0,0,0.12);">
        
        <div style="height: 120px; background: linear-gradient(135deg, var(--accent), var(--primary)); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
            <div style="position: absolute; background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%); width: 200px; height: 200px; border-radius: 50%;"></div>
            <i data-feather="shield" style="color: white; width: 48px; height: 48px; position: relative; z-index: 2;"></i>
        </div>

        <div class="p-5" style="background: rgba(255, 255, 255, 0.75);">
            <div class="text-center mb-4">
                <h3 class="mb-2" style="font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.8rem;">Verify Identity</h3>
                <p class="text-muted m-0" style="font-size: 0.95rem;">Enter the verification code sent to your email.</p>
            </div>
            
            <form method="POST" action="{{ route('otp.verify') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label mb-2" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px;">Email Address</label>
                    <div class="position-relative">
                        <i data-feather="mail" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                        <input type="email" name="email" class="form-control text-muted" style="padding-left: 42px; height: 48px; background: rgba(0,0,0,0.02); border-color: transparent;" value="{{ session('otp_email') }}" readonly required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label mb-2 d-flex justify-content-between" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px;">
                        <span>Secure Code</span>
                        <a href="{{ route('otp.request.form') }}" style="color: var(--primary); text-decoration: none; text-transform: none; letter-spacing: normal;">Resend Code</a>
                    </label>
                    <input type="text" name="otp" class="form-control text-center tracking-wide font-monospace fw-bold" style="height: 56px; font-size: 1.5rem; letter-spacing: 4px; background: rgba(255,255,255,0.9); box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);" maxlength="6" placeholder="------" required autofocus>
                </div>

                <div class="row">
                    <div class="col-sm-6 mb-4">
                        <label class="form-label mb-1" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px;">New Password</label>
                        <div class="position-relative">
                            <i data-feather="lock" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <input type="password" name="password" class="form-control" style="padding-left: 42px; height: 44px; background: rgba(255,255,255,0.9);" required>
                        </div>
                    </div>
                    <div class="col-sm-6 mb-4">
                        <label class="form-label mb-1" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px;">Confirm</label>
                        <div class="position-relative">
                            <i data-feather="check-circle" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <input type="password" name="password_confirmation" class="form-control" style="padding-left: 42px; height: 44px; background: rgba(255,255,255,0.9);" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 mt-2" style="height: 52px; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    Set New Password <i data-feather="shield-fill" style="width: 18px;"></i>
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
