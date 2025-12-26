@extends('layouts.zk')

@section('title', 'Connection Status')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm mt-5">
            <div class="card-header bg-transparent border-0 py-4 text-center">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-router text-primary" style="font-size: 40px;"></i>
                </div>
                <h4 class="fw-bold mb-1">Network Connection</h4>
                <p class="text-muted small">Device Connectivity Status</p>
            </div>
            
            <div class="card-body px-5 pb-5">
                @if($status)
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-center py-4 mb-4">
                        <div class="mb-2"><i class="bi bi-check-circle-fill text-success fs-1"></i></div>
                        <h5 class="fw-bold text-success mb-1">Connected Successfully</h5>
                        <p class="mb-0 small text-success">Device is online and reachable.</p>
                    </div>
                @else
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-center py-4 mb-4">
                        <div class="mb-2"><i class="bi bi-x-circle-fill text-danger fs-1"></i></div>
                        <h5 class="fw-bold text-danger mb-1">Connection Failed</h5>
                        <p class="mb-0 small text-danger">Device is unreachable.</p>
                    </div>
                @endif

                <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                    <div class="list-group-item d-flex justify-content-between align-items-center bg-light">
                        <span class="text-muted small fw-bold text-uppercase">Configuration</span>
                        <span><i class="bi bi-gear-fill text-muted"></i></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted"><i class="bi bi-wifi me-2"></i>IP Address</span>
                        <span class="fw-bold font-monospace">{{ $ip }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted"><i class="bi bi-door-open me-2"></i>Port</span>
                        <span class="fw-bold font-monospace">{{ $port }}</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <span class="text-muted"><i class="bi bi-activity me-2"></i>Protocol</span>
                        <span class="badge bg-secondary">UDP</span>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <a href="{{ route('zk.connect') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-arrow-repeat me-2"></i>Test Connection Again
                    </a>
                    <a href="{{ route('zk.dashboard') }}" class="btn btn-link text-muted ms-2">Return to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
