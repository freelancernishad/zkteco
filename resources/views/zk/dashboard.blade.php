@extends('layouts.zk')

@section('title', 'Dashboard')

@section('content')
<!-- Stats Overview -->
<div class="row mb-5">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon" style="background-color: #e0e7ff; color: #4338ca;">
                <i class="bi bi-people-fill"></i>
            </div>
            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Total Users</h6>
            <h2 class="fw-bold mb-0">{{ isset($users) ? count($users) : 0 }}</h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon" style="background-color: #dcfce7; color: #15803d;">
                <i class="bi bi-fingerprint"></i>
            </div>
            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Logs (Today)</h6>
            <!-- We can pass specialized counts from controller for Dashboard -->
            <h2 class="fw-bold mb-0" id="dashboardLogCount">{{ $todayLogsCount ?? 0 }}</h2>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon" style="background-color: #fee2e2; color: #b91c1c;">
                <i class="bi bi-cpu"></i>
            </div>
            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Device Platform</h6>
            <h2 class="fw-bold mb-0">ZKTeco K50</h2>
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
@endsection
