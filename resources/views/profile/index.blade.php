@extends('layouts.app')

@section('content')
<div class="mb-4">
    <h2 class="h4 mb-0 text-gray-800 fw-bold">My Profile</h2>
    <p class="text-muted small">Manage your account settings and personal information.</p>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body bg-white rounded">
        <div class="row">
            <div class="col-md-3 text-center border-end">
                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 100px; height: 100px; font-size: 2.5rem; color: #6c757d;">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <h5 class="fw-bold mb-1">{{ auth()->user()->name }}</h5>
                <p class="text-muted small mb-0">{{ ucfirst(auth()->user()->role) }}</p>
            </div>
            <div class="col-md-9 ps-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">Account Details</h6>
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted fw-bold">Full Name</div>
                    <div class="col-sm-9">{{ auth()->user()->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted fw-bold">Email Address</div>
                    <div class="col-sm-9">{{ auth()->user()->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-3 text-muted fw-bold">Role</div>
                    <div class="col-sm-9"><span class="badge bg-primary">{{ ucfirst(auth()->user()->role) }}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
