@extends('layouts.zk')

@section('title', 'Attendance Logs')

@section('content')
    <div class="card">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">Attendance Logs</h5>
        </div>

        <!-- Filter Toolbar -->
        <div class="card-body border-bottom bg-white py-4 px-4 sticky-top shadow-sm" style="top: 72px; z-index: 990;">
            <form action="{{ route('zk.logs') }}" method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-auto">
                        <label class="form-label small text-muted text-uppercase fw-bold">Quick Filters</label>
                        <div class="btn-group d-block">
                            <a href="{{ route('zk.logs', ['filter' => 'today']) }}"
                                class="btn btn-outline-light text-dark {{ request('filter') == 'today' || !request('filter') && !request('start_date') ? 'active bg-primary text-white border-primary' : '' }}">Today</a>
                            <a href="{{ route('zk.logs', ['filter' => 'yesterday']) }}"
                                class="btn btn-outline-light text-dark {{ request('filter') == 'yesterday' ? 'active bg-primary text-white border-primary' : '' }}">Yesterday</a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small text-muted text-uppercase fw-bold">Date Range</label>
                        <div class="input-group">
                            <input type="date" name="start_date" class="form-control border-end-0"
                                value="{{ request('start_date') }}">
                            <span class="input-group-text bg-white border-start-0 border-end-0 text-muted px-2">to</span>
                            <input type="date" name="end_date" class="form-control border-start-0"
                                value="{{ request('end_date') }}">
                        </div>
                    </div>
                    <div class="col-auto">
                        <label class="form-label small text-muted text-uppercase fw-bold">Search User</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0 text-muted">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" id="logSearchInput" class="form-control border-start-0 ps-0" placeholder="User ID / Badge...">
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-dark px-4"><i class="bi bi-funnel me-1"></i> Apply
                            Filter</button>
                        <a href="{{ route('zk.logs') }}" class="btn btn-link text-muted text-decoration-none ms-2">Reset</a>
                    </div>
                    <div class="col text-end ms-auto">
                        <label class="form-label d-block text-white">Action</label>
                        <a href="{{ route('zk.logs.clear') }}" class="btn btn-danger text-white"
                            onclick="return confirm('WARNING: This will delete ALL attendance records from the device permanently. This cannot be undone. Are you sure?');">
                            <i class="bi bi-trash-fill me-1"></i> Clear Logs
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="">
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
                                <td class="fw-bold ps-4">{{ date('h:i:s A', strtotime($log->timestamp)) }} <small
                                        class="text-muted ms-1">{{ date('M d, Y', strtotime($log->timestamp)) }}</small></td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark">{{ $log->user ? $log->user->name : $log->user_id }}</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">Badge: {{ $log->user_id }}</small>
                                    </div>
                                </td>
                                <td>
                                    @if($log->type == 1)
                                        <span class="text-success"><i class="bi bi-box-arrow-right me-1"></i>Check Out</span>
                                    @else
                                        <span class="text-primary"><i class="bi bi-box-arrow-in-right me-1"></i>Check In</span>
                                    @endif
                                </td>
                                <td>
                                    @if($log->state == 1) <span class="badge bg-primary"><i
                                        class="bi bi-fingerprint me-1"></i>Fingerprint</span>
                                    @elseif($log->state == 4 || $log->state == 2) <span class="badge bg-info text-dark"><i
                                        class="bi bi-credit-card me-1"></i>Card</span>
                                    @elseif($log->state == 0) <span class="badge bg-secondary"><i
                                        class="bi bi-key me-1"></i>Password</span>
                                    @elseif($log->state == 15) <span class="badge bg-warning text-dark"><i
                                        class="bi bi-person-bounding-box me-1"></i>Face ID</span>
                                    @else <span class="badge bg-light text-muted">Other ({{$log->state}})</span> @endif
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
    <style>
        /* Sticky Table Header for Logs - Page Level */
        .table thead th {
            position: sticky;
            top: 184px; /* Navbar (72px) + Filter Toolbar (~112px max) */
            background-color: #f8f9fa !important;
            z-index: 980;
            border-bottom: 2px solid #dee2e6 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
    </style>
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

        // Search Functionality
        let currentSearchQuery = '';
        const searchInput = document.getElementById('logSearchInput');
        
        function filterRows() {
            const tbody = document.getElementById('logsTableBody');
            const rows = tbody.querySelectorAll('tr:not(.text-center)');
            
            rows.forEach(row => {
                const nameNode = row.querySelector('td:nth-child(2) span');
                const badgeNode = row.querySelector('td:nth-child(2) small');
                
                if (!nameNode || !badgeNode) return;
                
                const nameText = nameNode.textContent.toLowerCase();
                const badgeText = badgeNode.textContent.toLowerCase();
                
                if (nameText.includes(currentSearchQuery) || badgeText.includes(currentSearchQuery)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                currentSearchQuery = this.value.toLowerCase().trim();
                filterRows();
            });
        }

        // Poll for new data every 5 seconds (Specific for update Table)
        setInterval(function () {
            fetch("{{ route('zk.api.logs') }}?" + getQueryParams())
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('logsTableBody');
                    let html = '';

                    if (data.data.length > 0) {
                        data.data.forEach(log => {
                            let stateHtml = '';
                            if (log.type == 1) {
                                stateHtml = '<span class="text-success"><i class="bi bi-box-arrow-right me-1"></i>Check Out</span>';
                            } else {
                                stateHtml = '<span class="text-primary"><i class="bi bi-box-arrow-in-right me-1"></i>Check In</span>';
                            }

                            let typeText = `<span class="badge bg-light text-muted">Other (${log.state})</span>`;
                            if (log.state == 1) typeText = '<span class="badge bg-primary"><i class="bi bi-fingerprint me-1"></i>Fingerprint</span>';
                            else if (log.state == 4 || log.state == 2) typeText = '<span class="badge bg-info text-dark"><i class="bi bi-credit-card me-1"></i>Card</span>';
                            else if (log.state == 0) typeText = '<span class="badge bg-secondary"><i class="bi bi-key me-1"></i>Password</span>';
                            else if (log.state == 15) typeText = '<span class="badge bg-warning text-dark"><i class="bi bi-person-bounding-box me-1"></i>Face ID</span>';

                            // Format Date
                            const dateObj = new Date(log.timestamp);
                            const timeStr = dateObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', second: 'numeric', hour12: true });
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
                    if (currentSearchQuery) filterRows();
                })
                .catch(error => console.error('Error fetching logs:', error));
        }, 5000); 
    </script>
@endsection