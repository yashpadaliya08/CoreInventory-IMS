@extends('layouts.app')

@push('styles')
<style>
    .page-header { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 24px; }
    .filter-panel { background: rgba(255, 255, 255, 0.5); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.8); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.02); }
    .event-badge { padding: 5px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: 800; letter-spacing: 0.5px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 5px; }
    .event-created { background: rgba(34,197,94,0.1); color: #16a34a; border: 1px solid rgba(34,197,94,0.2); }
    .event-updated { background: rgba(245,158,11,0.1); color: #d97706; border: 1px solid rgba(245,158,11,0.2); }
    .event-deleted { background: rgba(239,68,68,0.1); color: #dc2626; border: 1px solid rgba(239,68,68,0.2); }
    .log-badge { background: rgba(99,102,241,0.1); color: var(--primary); border: 1px solid rgba(99,102,241,0.2); padding: 4px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 700; font-family: 'Outfit'; text-transform: uppercase; letter-spacing: 0.5px; }
    .props-box { background: rgba(0,0,0,0.03); border-radius: 8px; padding: 10px 14px; font-size: 0.8rem; font-family: 'Outfit'; color: var(--text-muted); max-width: 400px; word-break: break-all; }
    .causer-tag { font-family: 'Outfit'; font-weight: 600; color: var(--text-main); }
</style>
@endpush

@section('content')
<div class="fade-in">
    <div class="page-header">
        <div>
            <h2 class="m-0" style="font-family: 'Outfit'; font-weight: 700; font-size: 2rem; color: var(--text-main); letter-spacing: -0.5px;">Activity Audit Log</h2>
            <p class="text-muted m-0 mt-1" style="font-size: 1rem;">Complete accountability trail of all system operations.</p>
        </div>
    </div>

    <div class="filter-panel">
        <form method="GET" action="{{ route('activity-log.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Search</label>
                <div class="position-relative">
                    <i data-feather="search" style="position: absolute; top: 12px; left: 14px; color: var(--text-muted); width: 18px;"></i>
                    <input type="text" name="search" class="form-control" style="padding-left: 42px; height: 44px; background: rgba(255,255,255,0.9);" placeholder="Search descriptions or properties..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Module</label>
                <select name="log_name" class="form-select" style="height: 44px; background: rgba(255,255,255,0.9);">
                    <option value="">All Modules</option>
                    @foreach($logNames as $logName)
                        <option value="{{ $logName }}" {{ request('log_name') == $logName ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $logName)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label text-muted" style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px;">Event Type</label>
                <select name="event" class="form-select" style="height: 44px; background: rgba(255,255,255,0.9);">
                    <option value="">All Events</option>
                    <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created</option>
                    <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100 d-flex justify-content-center align-items-center gap-2" style="height: 44px; background: rgba(0,0,0,0.05); color: var(--text-main); border: 1px solid rgba(0,0,0,0.1); font-weight: 600;">
                    <i data-feather="filter" style="width: 16px;"></i> Filter
                </button>
            </div>
        </form>
    </div>

    <div class="glass-panel overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead style="background: rgba(0,0,0,0.02);">
                    <tr>
                        <th class="ps-4 border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">TIMESTAMP</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">USER</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">EVENT</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">MODULE</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">DESCRIPTION</th>
                        <th class="border-0" style="color: var(--text-muted); font-size: 0.75rem; font-weight: 700; letter-spacing: 1px;">CHANGES</th>
                    </tr>
                </thead>
                <tbody style="border-top: 2px solid rgba(0,0,0,0.05);">
                    @forelse($activities as $activity)
                    <tr>
                        <td class="ps-4 text-muted" style="font-family: 'Outfit'; font-size: 0.9rem; font-weight: 500; white-space: nowrap;">
                            {{ $activity->created_at->format('d M Y, H:i') }}
                        </td>
                        <td>
                            @if($activity->causer)
                                <span class="causer-tag">{{ $activity->causer->name }}</span>
                                <div class="text-muted" style="font-size: 0.72rem;">{{ $activity->causer->email }}</div>
                            @else
                                <span class="text-muted">System</span>
                            @endif
                        </td>
                        <td>
                            @php $event = $activity->event ?? 'unknown'; @endphp
                            <span class="event-badge event-{{ $event }}">
                                @if($event === 'created')
                                    <i data-feather="plus-circle" style="width: 12px;"></i>
                                @elseif($event === 'updated')
                                    <i data-feather="edit-3" style="width: 12px;"></i>
                                @elseif($event === 'deleted')
                                    <i data-feather="trash-2" style="width: 12px;"></i>
                                @endif
                                {{ ucfirst($event) }}
                            </span>
                        </td>
                        <td>
                            <span class="log-badge">{{ str_replace('_', ' ', $activity->log_name) }}</span>
                        </td>
                        <td>
                            <div style="font-size: 0.9rem; color: var(--text-main); font-weight: 500;">{{ $activity->description }}</div>
                        </td>
                        <td>
                            @if($activity->properties && $activity->properties->count() > 0)
                                <div class="props-box">
                                    @if($activity->properties->has('old'))
                                        @foreach($activity->properties['attributes'] ?? [] as $key => $newVal)
                                            @php $oldVal = $activity->properties['old'][$key] ?? '—'; @endphp
                                            <div><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> <span style="color: #dc2626; text-decoration: line-through;">{{ $oldVal }}</span> → <span style="color: #16a34a;">{{ $newVal }}</span></div>
                                        @endforeach
                                    @elseif($activity->properties->has('attributes'))
                                        @foreach($activity->properties['attributes'] as $key => $val)
                                            <div><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $val }}</div>
                                        @endforeach
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i data-feather="activity" style="width: 48px; height: 48px; color: var(--text-muted); opacity: 0.3; margin-bottom: 16px;"></i>
                            <h5 style="font-family: 'Outfit'; font-weight: 600; color: var(--text-main);">No Activity Recorded</h5>
                            <p class="text-muted" style="max-width: 300px; margin: 0 auto;">Activity logs will appear automatically as users interact with the system.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4" style="opacity: 0.9;">
        @if(method_exists($activities, 'links'))
            {{ $activities->links('pagination::bootstrap-5') }}
        @endif
    </div>
</div>
@endsection
