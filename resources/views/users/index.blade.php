@extends('layouts.app')

@push('styles')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; }
    .role-badge { padding: 6px 14px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 5px; }
    .role-admin   { background: rgba(239, 68, 68, 0.12); color: #dc2626; border: 1px solid rgba(239,68,68,0.25); }
    .role-manager { background: rgba(245, 158, 11, 0.12); color: #d97706; border: 1px solid rgba(245,158,11,0.25); }
    .role-staff   { background: rgba(148, 163, 184, 0.15); color: #475569; border: 1px solid rgba(148,163,184,0.25); }
    .action-btn { display: inline-flex; align-items: center; gap: 6px; height: 36px; padding: 0 14px; border-radius: 8px; font-size: 0.82rem; font-weight: 600; transition: all 0.2s; }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">User Management</h2>
            <p class="text-muted m-0 mt-1">Administer roles and access levels. Admin-exclusive panel.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="alert border-0 mb-4" style="background: rgba(16,185,129,0.1); color: #065f46; border-radius: 12px; border-left: 4px solid #10b981 !important; border-left-width: 4px !important;" role="alert">
        <i data-feather="check-circle" style="width: 16px; margin-right: 8px;"></i>{{ session('success') }}
    </div>
    @endif

    <div class="glass-panel overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead style="background: rgba(0,0,0,0.02);">
                    <tr>
                        <th class="ps-4 border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">User Identity</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Email Address</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Assigned Role</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">Registered</th>
                        <th class="border-0 text-end pe-4"></th>
                    </tr>
                </thead>
                <tbody style="border-top: 2px solid rgba(0,0,0,0.05);">
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold" style="color: var(--text-main); font-size: 1.05rem;">{{ $user->name }}</div>
                            @if($user->id === auth()->id())
                                <span style="font-size: 0.7rem; font-weight: 700; color: var(--primary); letter-spacing: 0.5px;">● YOU</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td>
                            <span class="role-badge role-{{ $user->role }}">
                                @if($user->role === 'admin') <i data-feather="shield" style="width: 12px;"></i>
                                @elseif($user->role === 'manager') <i data-feather="briefcase" style="width: 12px;"></i>
                                @else <i data-feather="user" style="width: 12px;"></i>
                                @endif
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="text-muted" style="font-family: 'Outfit'; font-weight: 500; font-size: 0.95rem;">
                            {{ $user->created_at->format('M d, Y') }}
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('users.edit', $user) }}" class="action-btn btn btn-light" style="border: 1px solid rgba(0,0,0,0.1);">
                                    <i data-feather="edit-2" style="width: 14px;"></i> Edit Role
                                </a>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="m-0"
                                      onsubmit="return confirm('Remove {{ $user->name }}? This is irreversible.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn btn btn-light" style="border: 1px solid rgba(239,68,68,0.3); color: #dc2626;">
                                        <i data-feather="trash-2" style="width: 14px;"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
