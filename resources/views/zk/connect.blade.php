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
                <!-- Status Placeholders -->
                <div id="status-loading" class="alert alert-info border-0 bg-info bg-opacity-10 text-center py-4 mb-4">
                    <div class="spinner-border text-info mb-2" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <h5 class="fw-bold text-info mb-1">Checking Connection...</h5>
                    <p class="mb-0 small text-info">Attempting to reach device...</p>
                </div>

                <div id="status-success" class="alert alert-success border-0 bg-success bg-opacity-10 text-center py-4 mb-4 d-none">
                    <div class="mb-2"><i class="bi bi-check-circle-fill text-success fs-1"></i></div>
                    <h5 class="fw-bold text-success mb-1">Connected Successfully</h5>
                    <p class="mb-0 small text-success">Device is online and reachable.</p>
                </div>

                <div id="status-error" class="alert alert-danger border-0 bg-danger bg-opacity-10 text-center py-4 mb-4 d-none">
                    <div class="mb-2"><i class="bi bi-x-circle-fill text-danger fs-1"></i></div>
                    <h5 class="fw-bold text-danger mb-1">Connection Failed</h5>
                    <p class="mb-0 small text-danger" id="error-message-text">Device is unreachable.</p>
                </div>

                <div class="list-group list-group-flush border rounded-3 overflow-hidden mb-4">
                    <div class="list-group-item d-flex justify-content-between align-items-center bg-light">
                        <span class="text-muted small fw-bold text-uppercase">Connection Config</span>
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

                <div id="device-info-container" class="d-none">
                    <div class="list-group list-group-flush border rounded-3 overflow-hidden">
                        <div class="list-group-item d-flex justify-content-between align-items-center bg-light">
                            <span class="text-muted small fw-bold text-uppercase">Device Information</span>
                            <span><i class="bi bi-cpu-fill text-muted"></i></span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted">Device Name</span>
                            <span class="fw-bold" id="info-device-name">-</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted">Serial Number</span>
                            <span class="fw-bold font-monospace" id="info-serial-number">-</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted">Firmware Version</span>
                            <span class="fw-bold" id="info-version">-</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted">Platform / OS</span>
                            <span class="fw-bold" id="info-platform-os">-</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted">Device Time</span>
                            <span class="fw-bold" id="info-time">-</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <button onclick="checkConnection()" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-arrow-repeat me-2"></i>Check Now
                    </button>
                    <a href="{{ route('zk.dashboard') }}" class="btn btn-link text-muted ms-2">Return to Dashboard</a>
                </div>
                
                <p class="text-muted small text-center mt-3 mb-0" id="last-updated">Last checked: Never</p>
            </div>
        </div>
    </div>
</div>

<script>
    let pollInterval;

    function updateStatusUI(isSuccess, data = null) {
        const loading = document.getElementById('status-loading');
        const success = document.getElementById('status-success');
        const error = document.getElementById('status-error');
        const errorMsg = document.getElementById('error-message-text'); // New element we need to create
        const infoContainer = document.getElementById('device-info-container');
        const lastUpdated = document.getElementById('last-updated');

        loading.classList.add('d-none');
        
        if (isSuccess && data) {
            success.classList.remove('d-none');
            error.classList.add('d-none');
            infoContainer.classList.remove('d-none');

            document.getElementById('info-device-name').textContent = data.device_name;
            document.getElementById('info-serial-number').textContent = data.serial_number;
            document.getElementById('info-version').textContent = data.version;
            document.getElementById('info-platform-os').textContent = data.platform + ' / ' + data.os_version;
            document.getElementById('info-time').textContent = data.device_time;
        } else {
            success.classList.add('d-none');
            error.classList.remove('d-none');
            infoContainer.classList.add('d-none');
            
            // Update error text if element exists
            if(errorMsg) {
                 errorMsg.textContent = typeof data === 'string' ? data : "Device is unreachable.";
            }
        }

        const now = new Date();
        lastUpdated.textContent = 'Last checked: ' + now.toLocaleTimeString();
    }

    function checkConnection() {
        // Show loading state if it's a manual check (optional, keeping it subtle for auto-polling)
        // document.getElementById('status-loading').classList.remove('d-none');
        // document.getElementById('status-success').classList.add('d-none');
        // document.getElementById('status-error').classList.add('d-none');

        fetch('{{ url("/zk/info") }}')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.message && data.message.includes('Connection Failed')) { // Check for failure keywords
                    updateStatusUI(false, data.message); // Pass error message
                    scheduleNextPoll(false);
                } else if (data.version) { // Check for success indicators
                    updateStatusUI(true, data);
                    scheduleNextPoll(true);
                } else {
                     // Fallback for unexpected response
                    updateStatusUI(false, data.message || "Unknown Error");
                    scheduleNextPoll(false);
                }
            })
            .catch(error => {
                console.error('Error fetching status:', error);
                updateStatusUI(false, "Network Error: Could not reach server.");
                scheduleNextPoll(false);
            });
    }

    function scheduleNextPoll(lastWasSuccess) {
        clearTimeout(pollInterval);
        const delay = lastWasSuccess ? 60000 : 10000; // 60s if success, 10s if fail
        pollInterval = setTimeout(checkConnection, delay);
    }

    // Initial check on load
    document.addEventListener('DOMContentLoaded', checkConnection);
</script>
@endsection
