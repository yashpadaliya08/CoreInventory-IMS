@extends('layouts.app')

@push('styles')
<style>
    .page-header { margin-bottom: 32px; }
    .glass-form-card { background: rgba(255, 255, 255, 0.6); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.8); border-radius: var(--radius-lg); padding: 32px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .form-glass { background: rgba(255, 255, 255, 0.9); border: 1px solid rgba(0,0,0,0.05); border-radius: 8px; padding: 12px 16px; height: 48px; transition: all 0.2s; }
    .form-glass:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    .section-title { font-family: 'Outfit'; font-weight: 700; color: var(--text-main); font-size: 1.25rem; display: flex; align-items: center; gap: 8px; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 2px solid rgba(0,0,0,0.03); }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <a href="{{ route('users.index') }}" class="text-muted text-decoration-none fw-bold" style="font-size: 0.85rem;">
                <i data-feather="arrow-left" style="width: 14px;"></i> Back to Users
            </a>
            <h2 class="m-0 mt-2" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">
                Modify Access — {{ $user->name }}
            </h2>
        </div>
    </div>

    <div class="row g-4 d-flex justify-content-center">
        <div class="col-lg-6">
            <div class="glass-form-card">
                <h6 class="section-title"><i data-feather="shield" style="color: var(--primary);"></i> Role Assignment</h6>

                <form action="{{ route('users.update', $user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Access Role</label>
                        <div class="position-relative">
                            <i data-feather="shield" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <select name="role" class="form-select form-glass" style="padding-left: 42px;" required>
                                <option value="admin"   {{ $user->role === 'admin'   ? 'selected' : '' }}>Admin — Full system access</option>
                                <option value="manager" {{ $user->role === 'manager' ? 'selected' : '' }}>Manager — Create, validate, no delete</option>
                                <option value="staff"   {{ $user->role === 'staff'   ? 'selected' : '' }}>Staff — Read-only access</option>
                            </select>
                        </div>
                        @error('role') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-5 pt-4 border-top">
                        <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">New Password (Optional)</label>
                        <p class="text-muted small mb-3">Leave blank to keep the current password unchanged.</p>
                        <div class="position-relative mb-3">
                            <i data-feather="lock" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <input type="password" name="password" class="form-control form-glass" style="padding-left: 42px;" placeholder="New password (min. 8 chars)">
                        </div>
                        <div class="position-relative">
                            <i data-feather="check-circle" style="position: absolute; top: 14px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                            <input type="password" name="password_confirmation" class="form-control form-glass" style="padding-left: 42px;" placeholder="Confirm new password">
                        </div>
                        @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="btn btn-primary d-flex justify-content-center align-items-center gap-2 w-100" style="height: 54px; font-size: 1.1rem; border-radius: 12px; box-shadow: 0 8px 20px rgba(99,102,241,0.3);">
                        <i data-feather="save"></i> Apply Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
