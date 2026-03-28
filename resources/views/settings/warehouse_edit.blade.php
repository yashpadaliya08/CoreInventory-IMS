@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('settings.index') }}" class="btn btn-sm btn-outline-secondary mb-3">&larr; Back to Settings</a>
    <h2 class="h4 mb-0 text-gray-800 fw-bold">Edit Warehouse: {{ $warehouse->name }}</h2>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-4 bg-light">
            <div class="card-body">
                <form action="{{ route('settings.warehouse.update', $warehouse) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Warehouse Name</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name', $warehouse->name) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Code / Abbr.</label>
                        <input type="text" name="code" class="form-control" required value="{{ old('code', $warehouse->code) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">Address (Optional)</label>
                        <textarea name="location_address" class="form-control" rows="3">{{ old('location_address', $warehouse->location_address) }}</textarea>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary px-4 fw-bold">Update Warehouse</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
