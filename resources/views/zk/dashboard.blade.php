@extends('layouts.zk')

@section('title', 'Dashboard')

@section('content')
<!-- Stats Overview -->
<div class="row mb-5">
    <div class="col-md-4">
        <div class="card stat-card h-100 border-0">
            <div class="d-flex align-items-center mb-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-0 me-3">
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
        <div class="card stat-card h-100 border-0">
            <div class="d-flex align-items-center mb-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success mb-0 me-3">
                    <i class="bi bi-fingerprint"></i>
                </div>
                <div>
                    <h6 class="text-uppercase text-muted small fw-bold mb-1 ls-1">Logs (Today)</h6>
                    <h2 class="fw-bold mb-0 text-dark" id="dashboardLogCount">{{ $todayLogsCount ?? 0 }}</h2>
                </div>
            </div>
            <div class="mt-auto">
                <small class="text-muted small">Real-time updates enabled</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card h-100 border-0">
             <div class="d-flex align-items-center mb-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger mb-0 me-3">
                    <i class="bi bi-cpu-fill"></i>
                </div>
                <div>
                    <h6 class="text-uppercase text-muted small fw-bold mb-1 ls-1">Device Status</h6>
                    <h2 class="fw-bold mb-0 text-dark">Online</h2>
                </div>
            </div>
             <div class="mt-auto">
                <small class="text-muted small">Device: ZKTeco K50</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/attendance-app-4467008-3729938.png" alt="Welcome" style="max-width: 300px;">
                <h3 class="mt-4 fw-bold">Welcome to Smart Access</h3>
                <p class="text-muted">Select an option from the sidebar to manage users or view attendance logs.</p>
            </div>
        </div>
    </div>
</div>
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
