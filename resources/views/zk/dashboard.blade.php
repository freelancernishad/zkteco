@extends('layouts.zk')

@section('title', 'Dashboard')

@section('content')
<!-- Stats Overview -->
<div class="row mb-5">
    <div class="col-md-4">
        <div class="card stat-card h-100 border-0 border-start border-4 border-primary shadow-sm">
            <div class="d-flex align-items-center mb-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-0 me-3 rounded-circle">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                     <h6 class="text-uppercase text-muted small fw-bold mb-1 ls-1">Total Users</h6>
                     <h2 class="fw-bold mb-0 text-dark">{{ isset($users) ? count($users) : 0 }}</h2>
                </div>
            </div>
            <div class="mt-auto">
                 <small class="text-success fw-medium"><i class="bi bi-arrow-up-short"></i> Active</small> <span class="text-muted small">on device</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card h-100 border-0 border-start border-4 border-success shadow-sm">
            <div class="d-flex align-items-center mb-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success mb-0 me-3 rounded-circle">
                    <i class="bi bi-fingerprint"></i>
                </div>
                <div>
                    <h6 class="text-uppercase text-muted small fw-bold mb-1 ls-1">Logs (Today)</h6>
                    <h2 class="fw-bold mb-0 text-dark" id="dashboardLogCount">{{ $todayLogsCount ?? 0 }}</h2>
                </div>
            </div>
            <div class="mt-auto">
                <span class="badge bg-success bg-opacity-10 text-success">Real-time</span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card h-100 border-0 border-start border-4 border-secondary shadow-sm" id="dashboard-device-status-card">
             <div class="d-flex align-items-center mb-3">
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary mb-0 me-3 rounded-circle" id="dashboard-device-status-icon">
                    <i class="bi bi-router"></i>
                </div>
                <div>
                    <h6 class="text-uppercase text-muted small fw-bold mb-1 ls-1">Device Status</h6>
                    <h2 class="fw-bold mb-0 text-dark" id="dashboard-device-status-title">Checking...</h2>
                </div>
            </div>
             <div class="mt-auto">
                <small class="text-muted small" id="dashboard-device-status-text">Connecting...</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Activity Table -->
    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Activity</h6>
                <a href="{{ route('zk.logs') }}" class="btn btn-sm btn-light text-muted">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-uppercase small text-muted">User</th>
                            <th class="text-uppercase small text-muted">Time</th>
                            <th class="text-uppercase small text-muted">State</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2 text-primary fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        {{ substr($log->user ? $log->user->name : 'U', 0, 1) }}
                                    </div>
                                    <span class="fw-bold text-dark small">{{ $log->user ? $log->user->name : 'ID: ' . $log->user_id }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold text-dark small d-block">{{ date('h:i:s A', strtotime($log->timestamp)) }}</span>
                                <span class="text-muted" style="font-size: 0.7rem;">{{ date('M d', strtotime($log->timestamp)) }}</span>
                            </td>
                            <td>
                                @if($log['state'] == 1)
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill">Out</span>
                                @else
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill">In</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted small">No recent activity found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="fw-bold mb-0">Quick Actions</h6>
            </div>
            <div class="card-body pt-0">
                <div class="d-grid gap-3">
                    <a href="{{ route('zk.force.sync') }}" class="btn btn-primary d-flex align-items-center justify-content-between p-3">
                        <span><i class="bi bi-arrow-repeat me-2"></i> Sync Data</span>
                        <i class="bi bi-chevron-right small"></i>
                    </a>
                    <a href="{{ route('zk.users.manager') }}" class="btn btn-light text-start d-flex align-items-center justify-content-between p-3 border">
                        <span class="text-dark"><i class="bi bi-people me-2 text-muted"></i> Manage Users</span>
                        <i class="bi bi-chevron-right small text-muted"></i>
                    </a>
                    <a href="{{ route('zk.logs.clear') }}" class="btn btn-light text-start d-flex align-items-center justify-content-between p-3 border text-danger" onclick="return confirm('Are you sure you want to clear all logs?');">
                        <span><i class="bi bi-trash me-2"></i> Clear Logs</span>
                        <i class="bi bi-chevron-right small"></i>
                    </a>
                </div>
                
                <div class="mt-4 p-3 bg-light rounded-3 border border-warning border-opacity-25">
                    <h6 class="text-warning small fw-bold text-uppercase mb-2"><i class="bi bi-info-circle me-1"></i> System Info</h6>
                    <ul class="list-unstyled small mb-0 text-muted">
                         <li class="mb-1 d-flex justify-content-between"><span>Auto Sync:</span> <span class="text-success fw-bold">Active (5s)</span></li>
                         <li class="d-flex justify-content-between"><span>Webhook:</span> <span class="{{ env('ZK_WEBHOOK_URL') ? 'text-success' : 'text-secondary' }} fw-bold">{{ env('ZK_WEBHOOK_URL') ? 'Enabled' : 'Disabled' }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    setInterval(function() {
        // Fetch Today's Logs/Count via API
        fetch("{{ route('zk.api.logs') }}?filter=today")
            .then(response => response.json())
            .then(data => {
                const countEl = document.getElementById('dashboardLogCount');
                if (countEl) {
                    countEl.innerText = data.count;
                }
            })
            .catch(e => console.error("Dashboard Poll Error", e));
    }, 5000);
</script>
@endsection
