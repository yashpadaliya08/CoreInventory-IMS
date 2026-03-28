<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login attempt.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => __('The provided credentials do not match our records.'),
        ])->onlyInput('email');
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration.
     * New self-registered accounts always default to 'staff' (read-only).
     * Only an existing admin can promote a user to manager or admin.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'staff', // All self-registered accounts start as read-only staff
        ]);

        Auth::login($user);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Show the OTP request form (password reset — step 1).
     */
    public function showOtpRequestForm()
    {
        return view('auth.otp-request');
    }

    /**
     * Send OTP — stored in DB columns, not volatile session.
     * Rate limited: max 3 requests per 10 minutes per email address.
     */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        // Rate limiting — prevent OTP spam / brute-force enumeration
        $rateLimitKey = 'otp-request:' . Str::lower($request->email);
        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return back()->withErrors([
                'email' => "Too many OTP requests. Please wait {$seconds} seconds before trying again.",
            ]);
        }
        RateLimiter::hit($rateLimitKey, 600); // 10 minute decay window

        $user = User::where('email', $request->email)->firstOrFail();

        // Generate a 6-digit OTP and persist to the database
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        // Log OTP for development — replace with Mail::to($user)->send(new OtpMail($otp)) in production
        Log::info("CoreInventory OTP for [{$request->email}]: {$otp}");

        return redirect()->route('otp.verify.form')
            ->with('status', 'An OTP has been dispatched. Check the application log for now.');
    }

    /**
     * Show the OTP verification form (step 2).
     */
    public function showOtpVerifyForm()
    {
        return view('auth.otp-verify');
    }

    /**
     * Verify OTP from the database and reset the password.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email', 'exists:users,email'],
            'otp'      => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        // Validate OTP against DB record (not session)
        if (
            $user->otp_code !== $request->otp
            || $user->otp_expires_at === null
            || now()->greaterThan($user->otp_expires_at)
        ) {
            return back()->withErrors(['otp' => 'The OTP is invalid or has expired.']);
        }

        // Reset password and atomically clear OTP fields
        $user->update([
            'password'       => Hash::make($request->password),
            'otp_code'       => null,
            'otp_expires_at' => null,
        ]);

        // Clear rate limiter after successful password reset
        RateLimiter::clear('otp-request:' . Str::lower($request->email));

        return redirect()->route('login')
            ->with('status', 'Password has been reset successfully. Please log in.');
    }

    /**
     * Log out the authenticated user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
