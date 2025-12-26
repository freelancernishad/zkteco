@extends('layouts.zk')

@section('title', 'Attendance Logs')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="fw-bold mb-0">Attendance Logs</h5>
    </div>

    <!-- Filter Toolbar -->
    <form action="{{ route('zk.logs') }}" method="GET" class="bg-light border-bottom p-3">
        <div class="row g-3 align-items-end">
            <div class="col-auto">
                <div class="btn-group">
                    <a href="{{ route('zk.logs', ['filter' => 'today']) }}" class="btn btn-white border {{ request('filter') == 'today' || !request('filter') && !request('start_date') ? 'active bg-white text-primary border-primary' : '' }}">Today</a>
                    <a href="{{ route('zk.logs', ['filter' => 'yesterday']) }}" class="btn btn-white border {{ request('filter') == 'yesterday' ? 'active bg-white text-primary border-primary' : '' }}">Yesterday</a>
                </div>
            </div>
            <div class="col-auto border-start ps-4">
                <div class="input-group">
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    <span class="input-group-text bg-transparent border-start-0 border-end-0 text-muted">to</span>
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-dark"><i class="bi bi-funnel me-1"></i> Filter</button>
                <a href="{{ route('zk.logs') }}" class="btn btn-link text-muted text-decoration-none">Reset</a>
            </div>
            <div class="col text-end">
                    <a href="{{ route('zk.logs.clear') }}" class="btn btn-outline-danger" onclick="return confirm('WARNING: This will delete ALL attendance records from the device permanently. This cannot be undone. Are you sure?');">
                    <i class="bi bi-trash-fill me-1"></i> Clear Logs
                </a>
            </div>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table align-middle table-striped mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Time</th>
                    <th>User Badge</th>
                    <th>State</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody id="logsTableBody">
                @if(isset($attendance) && count($attendance) > 0)
                    @foreach($attendance as $log)
                    <tr>
                        <td class="fw-bold ps-4">{{ date('h:i A', strtotime($log->timestamp)) }} <small class="text-muted ms-1">{{ date('M d, Y', strtotime($log->timestamp)) }}</small></td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-dark">{{ $log->user ? $log->user->name : $log->user_id }}</span>
                                <small class="text-muted" style="font-size: 0.75rem;">Badge: {{ $log->user_id }}</small>
                            </div>
                        </td>
                        <td>
                            @if($log['state'] == 1)
                                <span class="text-success"><i class="bi bi-box-arrow-right me-1"></i>Check Out</span>
                            @else
                                <span class="text-primary"><i class="bi bi-box-arrow-in-right me-1"></i>Check In</span>
                            @endif
                        </td>
                        <td>
                            @if($log['type'] == 1) <span class="badge bg-primary">Fingerprint</span>
                            @elseif($log['type'] == 0) <span class="badge bg-secondary">Password</span>
                            @elseif($log['type'] == 2) <span class="badge bg-info text-dark">Card</span>
                            @elseif($log['type'] == 15) <span class="badge bg-warning text-dark">Face ID</span>
                            @else <span class="badge bg-light text-muted">Unknown ({{$log['type']}})</span> @endif
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <div class="text-muted">
                                <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                No attendance records found.
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Populate global map for notifications (if needed)
    @if(isset($users))
        @foreach($users as $u)
            globalUserMap["{{ $u['userid'] }}"] = "{{ $u['name'] }}";
        @endforeach
    @endif

    // Use current filters for API request
    function getQueryParams() {
        const params = new URLSearchParams(window.location.search);
        return params.toString();
    }

    // Poll for new data every 5 seconds (Specific for update Table)
    setInterval(function() {
        fetch("{{ route('zk.api.logs') }}?" + getQueryParams())
            .then(response => response.json())
            .then(data => {
                const tbody = document.getElementById('logsTableBody');
                let html = '';
                
                if (data.data.length > 0) {
                    data.data.forEach(log => {
                        let stateHtml = '';
                        if (log.state == 1) {
                            stateHtml = '<span class="text-success"><i class="bi bi-box-arrow-right me-1"></i>Check Out</span>';
                        } else {
                            stateHtml = '<span class="text-primary"><i class="bi bi-box-arrow-in-right me-1"></i>Check In</span>';
                        }
                        
                        let typeText = `<span class="badge bg-light text-muted">Unknown (${log.type})</span>`;
                        if (log.type == 1) typeText = '<span class="badge bg-primary">Fingerprint</span>';
                        else if (log.type == 0) typeText = '<span class="badge bg-secondary">Password</span>';
                        else if (log.type == 2) typeText = '<span class="badge bg-info text-dark">Card</span>';
                        else if (log.type == 15) typeText = '<span class="badge bg-warning text-dark">Face ID</span>';

                        // Format Date
                        const dateObj = new Date(log.timestamp);
                        const timeStr = dateObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour24: true }); 
                        const dateStr = dateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

                        html += `
                            <tr>
                                <td class="fw-bold ps-4">${timeStr} <small class="text-muted ms-1">${dateStr}</small></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">${log.user ? log.user.name : log.user_id}</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">Badge: ${log.user_id}</small>
                                    </div>
                                </td>
                                <td>${stateHtml}</td>
                                <td>${typeText}</td>
                            </tr>
                        `;
                    });
                } else {
                    html = `<tr><td colspan="4" class="text-center py-5"><div class="text-muted"><i class="bi bi-calendar-x fs-1 d-block mb-3"></i>No attendance records found for this selection.</div></td></tr>`;
                }
                
                tbody.innerHTML = html;
            })
            .catch(error => console.error('Error fetching logs:', error));
    }, 5000); 
</script>
@endsection
