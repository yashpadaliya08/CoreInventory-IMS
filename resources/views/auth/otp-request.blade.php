@extends('layouts.app')

@section('content')
<div class="container d-flex justify-content-center">
    <div class="glass-panel overflow-hidden" style="max-width: 480px; width: 100%; border-radius: 24px; box-shadow: 0 24px 48px rgba(0,0,0,0.12);">
        
        <div style="height: 120px; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; position: relative; overflow: hidden;">
            <div style="position: absolute; background: radial-gradient(circle, rgba(255,255,255,0.3) 0%, transparent 70%); width: 200px; height: 200px; border-radius: 50%;"></div>
            <i data-feather="key" style="color: white; width: 48px; height: 48px; position: relative; z-index: 2;"></i>
        </div>

        <div class="p-5" style="background: rgba(255, 255, 255, 0.75);">
            <div class="text-center mb-4">
                <h3 class="mb-2" style="font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.8rem;">Reset Password</h3>
                <p class="text-muted m-0" style="font-size: 0.95rem;">Enter your email to receive a secure 6-digit verification code.</p>
            </div>
            
            <form method="POST" action="{{ route('otp.request') }}">
                @csrf
                <div class="mb-4">
                    <label class="form-label mb-2" style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px;">Email Address</label>
                    <div class="position-relative">
                        <i data-feather="mail" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                        <input type="email" name="email" class="form-control" style="padding-left: 42px; height: 48px; background: rgba(255,255,255,0.9);" required autofocus placeholder="you@example.com">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100 mb-4" style="height: 52px; font-size: 1.05rem; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    Send Secure OTP <i data-feather="send" style="width: 18px;"></i>
                </button>
                
                <div class="text-center pt-2 border-top" style="border-color: rgba(0,0,0,0.05) !important;">
                    <a href="{{ route('login') }}" class="mt-4 d-inline-block" style="color: var(--text-muted); font-weight: 500; text-decoration: none; font-size: 0.95rem;">
                        <i data-feather="arrow-left" style="width: 16px; margin-right: 4px; vertical-align: text-bottom;"></i>
                        Back to sign in
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
