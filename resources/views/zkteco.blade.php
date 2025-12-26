<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Access Dashboard</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; overflow-x: hidden; }
        
        #wrapper { display: flex; width: 100%; }
        #sidebar-wrapper { min-height: 100vh; margin-left: -16rem; transition: margin .25s ease-out; width: 16rem; font-size: 0.95rem; }
        #sidebar-wrapper .sidebar-heading { padding: 0.875rem 1.25rem; font-size: 1.2rem; font-weight: bold; color: #4338ca; }
        #sidebar-wrapper .list-group { width: 16rem; }
        #page-content-wrapper { min-width: 100vw; transition: margin .25s ease-out; }
        
        body.sb-sidenav-toggled #wrapper #sidebar-wrapper { margin-left: 0; }
        @media (min-width: 768px) {
            #sidebar-wrapper { margin-left: 0; }
            #page-content-wrapper { min-width: 0; width: 100%; }
            body.sb-sidenav-toggled #wrapper #sidebar-wrapper { margin-left: -16rem; }
        }

        .list-group-item { border: none; padding: 12px 20px; color: #4b5563; font-weight: 500; }
        .list-group-item:hover { background-color: #f9fafb; color: #111827; }
        .list-group-item.active { background-color: #4f46e5; color: white; }
        .list-group-item i { width: 20px; display: inline-block; text-align: center; margin-right: 8px; }

        .card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); margin-bottom: 24px; transition: transform 0.2s;}
        .card:hover { transform: translateY(-2px); }
        .stat-card { background: white; padding: 24px; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-bottom: 16px; }
        
        .navbar { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); background: white; padding: 0.75rem 1.5rem; }
        
        .table thead th { background-color: #f9fafb; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #6b7280; padding: 12px 24px; border-bottom: 1px solid #e5e7eb; }
        .table tbody td { padding: 16px 24px; vertical-align: middle; border-bottom: 1px solid #f3f4f6; }
        .badge { padding: 6px 12px; border-radius: 9999px; font-weight: 500; font-size: 0.75rem; }
        .badge-admin { background-color: #ede9fe; color: #5b21b6; }
        .badge-user { background-color: #d1fae5; color: #065f46; }
    </style>
</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-white border-end" id="sidebar-wrapper">
            <div class="sidebar-heading border-bottom bg-light">
                <i class="bi bi-shield-lock-fill me-2"></i>Smart Access
            </div>
            <div class="list-group list-group-flush py-2">
                <a href="#dashboard" class="list-group-item list-group-item-action bg-transparent active">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="#users" class="list-group-item list-group-item-action bg-transparent" onclick="document.getElementById('pills-users-tab').click()">
                    <i class="bi bi-people"></i> Users
                </a>
                <a href="#logs" class="list-group-item list-group-item-action bg-transparent" onclick="document.getElementById('pills-logs-tab').click()">
                    <i class="bi bi-clock-history"></i> Attendance Logs
                </a>
                <a href="#" class="list-group-item list-group-item-action bg-transparent mt-4 border-top text-danger" onclick="alert('Settings module pending implementation.')">
                    <i class="bi bi-gear"></i> Settings
                </a>
            </div>
        </div>
        
        <!-- Page Content -->
        <div id="page-content-wrapper">
            
            <!-- Top Navigation -->
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
                <div class="container-fluid px-4">
                    <button class="btn btn-outline-primary" id="sidebarToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0 align-items-center">
                            <li class="nav-item me-3">
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-3 border border-success border-opacity-25">
                                    <i class="bi bi-wifi me-2"></i>Device IP: 192.168.0.201
                                </span>
                            </li>
                             <li class="nav-item">
                                <a href="{{ route('zk.force.sync') }}" class="btn btn-primary btn-sm rounded-pill px-3">
                                    <i class="bi bi-arrow-repeat me-1"></i> Sync Now
                                </a>
                            </li>
                            <li class="nav-item dropdown ms-3">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                    <div class="bg-indigo-100 rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; background-color: #e0e7ff; color: #4338ca;">
                                        AD
                                    </div>
                                    <span class="small fw-bold">Admin</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 br-12" aria-labelledby="navbarDropdown">
                                    <li><a class="dropdown-item" href="#">Profile</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="#">Logout</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>

            <!-- Main Content -->
            <div class="container-fluid px-5 py-5">
                
                <!-- Alerts -->
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

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
                            <h6 class="text-muted text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Logs (Current View)</h6>
                            <h2 class="fw-bold mb-0" id="logsCount">{{ isset($attendance) ? count($attendance) : 0 }}</h2>
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

                <!-- Main Content Tabs --> <!-- Hidden Nav, controlled by Sidebar -->
                <div class="card">
                    <div class="card-header bg-white border-bottom-0 pb-0 d-none">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pills-users-tab" data-bs-toggle="pill" data-bs-target="#pills-users" type="button" role="tab"></button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="pills-logs-tab" data-bs-toggle="pill" data-bs-target="#pills-logs" type="button" role="tab"></button>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card-body p-0">
                        <div class="tab-content" id="pills-tabContent">
                            
                            <!-- USERS TAB -->
                            <div class="tab-pane fade show active" id="pills-users" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                                    <h5 class="fw-bold mb-0">User Management</h5>
                                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                        <i class="bi bi-plus-lg me-2"></i>Add User
                                    </button>
                                </div>
                                <div class="table-responsive p-0">
                                    <table class="table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th class="ps-4">UID</th>
                                                <th>User ID (Badge)</th>
                                                <th>Name</th>
                                                <th>Role</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($users) && count($users) > 0)
                                                @foreach($users as $user)
                                                <tr>
                                                    <td class="fw-bold text-secondary ps-4">#{{ $user['uid'] }}</td>
                                                    <td><span class="badge bg-light text-dark border">{{ $user['userid'] }}</span></td>
                                                    <td class="fw-bold">{{ $user['name'] }}</td>
                                                    <td>
                                                        @if($user['role'] == 14) 
                                                            <span class="badge badge-admin">Admin</span>
                                                        @else 
                                                            <span class="badge badge-user">User</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('zk.user.delete', $user['uid']) }}" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this user from the device?');">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="5" class="text-center py-5">
                                                        <div class="text-muted">
                                                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                                            No users found on device.
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- LOGS TAB -->
                            <div class="tab-pane fade" id="pills-logs" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center p-4 border-bottom">
                                     <h5 class="fw-bold mb-0">Attendance Logs</h5>
                                </div>

                                <!-- Filter Toolbar -->
                                <form action="{{ route('zk.dashboard') }}" method="GET" class="bg-light border-bottom p-3">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-auto">
                                            <div class="btn-group">
                                                <a href="{{ route('zk.dashboard', ['filter' => 'today']) }}" class="btn btn-white border {{ request('filter') == 'today' || !request('filter') && !request('start_date') ? 'active bg-white text-primary border-primary' : '' }}">Today</a>
                                                <a href="{{ route('zk.dashboard', ['filter' => 'yesterday']) }}" class="btn btn-white border {{ request('filter') == 'yesterday' ? 'active bg-white text-primary border-primary' : '' }}">Yesterday</a>
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
                                            <a href="{{ route('zk.dashboard') }}" class="btn btn-link text-muted text-decoration-none">Reset</a>
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
                                                    <td class="fw-bold ps-4">{{ date('h:i A', strtotime($log['timestamp'])) }} <small class="text-muted ms-1">{{ date('M d, Y', strtotime($log['timestamp'])) }}</small></td>
                                                    <td>
                                                        <div class="d-flex flex-column">
                                                            @if(isset($users))
                                                                @php 
                                                                    $uName = 'Unknown';
                                                                    foreach($users as $u) { if($u['userid'] == $log['user_id']) $uName = $u['name']; }
                                                                @endphp
                                                                <span class="fw-bold text-dark">{{ $uName }}</span>
                                                            @else
                                                                <span class="fw-bold text-dark">{{ $log['user_id'] }}</span>
                                                            @endif
                                                            <small class="text-muted" style="font-size: 0.75rem;">Badge: {{ $log['user_id'] }}</small>
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
                                                        @if($log['type'] == 1) Fingerprint 
                                                        @elseif($log['type'] == 0) Password 
                                                        @else Card/Other @endif
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

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Toast Container for Notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toastContainer">
        <!-- Toast Template (Hidden) -->
        <div id="toastTemplate" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <i class="bi bi-bell-fill me-2"></i>
                <strong class="me-auto">New Attendance</strong>
                <small>Just Now</small>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body fw-bold toast-message">
                New punch detected!
            </div>
        </div>
    </div>

    <!-- Modal: Add User -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('zk.user.store') }}" method="POST" class="modal-content">
                @csrf
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Add New User to Device</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- ... body same as before ... -->
                 <div class="modal-body">
                    <div class="alert alert-info small mb-3">
                        <i class="bi bi-info-circle me-1"></i> User will be added to the device immediately. Fingerprints must be registered on the device itself.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">User ID (Badge No)</label>
                        <input type="number" name="userid" class="form-control" placeholder="e.g. 1001" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="0" selected>Normal User</option>
                            <option value="14">Admin (Administrator)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Card Number (Optional)</label>
                        <input type="number" name="cardno" class="form-control" placeholder="RFID Card Number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (Optional)</label>
                        <input type="text" name="password" class="form-control" placeholder="Device Password">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar
        document.getElementById("sidebarToggle").addEventListener("click", function () {
            document.body.classList.toggle("sb-sidenav-toggled");
        });

        // 1. User Map (ID -> Name) for fast lookup
        const userMap = {};
        @if(isset($users))
            @foreach($users as $u)
                userMap["{{ $u['userid'] }}"] = "{{ $u['name'] }}";
            @endforeach
        @endif

        // Last known timestamp to detect NEW logs
        let lastKnownTimestamp = "{{ isset($attendance[0]) ? $attendance[0]['timestamp'] : '' }}";

        // Toast Container
        const toastContainer = document.getElementById('toastContainer');
        const toastTemplate = document.getElementById('toastTemplate');

        function showToast(name, state) {
            // Clone template
            const newToastEl = toastTemplate.cloneNode(true);
            newToastEl.id = ''; // Remove ID to avoid duplicates
            newToastEl.classList.remove('hide');
            
            // Set Content
            newToastEl.querySelector('.toast-message').innerText = `${name} just ${state}!`;
            
            // Append and Show
            toastContainer.appendChild(newToastEl);
            const toast = new bootstrap.Toast(newToastEl);
            toast.show();
            
            // Remove from DOM after hidden
            newToastEl.addEventListener('hidden.bs.toast', function () {
                newToastEl.remove();
            });
        }

        // Use current filters for API request
        function getQueryParams() {
            const params = new URLSearchParams(window.location.search);
            return params.toString();
        }

        // Poll for new data every 5 seconds
        setInterval(function() {
            fetch("{{ route('zk.api.logs') }}?" + getQueryParams())
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('logsTableBody');
                    let html = '';
                    
                    if (data.data.length > 0) {
                        // Check for NEW logs
                        const newLogs = data.data.filter(log => lastKnownTimestamp && log.timestamp > lastKnownTimestamp);
                        
                        if (newLogs.length > 0) {
                            [...newLogs].reverse().forEach(log => {
                                const userName = userMap[log.user_id] || "Unknown User (" + log.user_id + ")";
                                const stateText = log.state == 1 ? "Checked Out" : "Checked In";
                                showToast(userName, stateText);
                            });
                            lastKnownTimestamp = data.data[0].timestamp;
                        } else if (!lastKnownTimestamp && data.data.length > 0) {
                             lastKnownTimestamp = data.data[0].timestamp;
                        }

                        data.data.forEach(log => {
                             // Get Name from Map
                            const userName = userMap[log.user_id] || log.user_id;

                            let stateHtml = '';
                            if (log.state == 1) {
                                stateHtml = '<span class="text-success"><i class="bi bi-box-arrow-right me-1"></i>Check Out</span>';
                            } else {
                                stateHtml = '<span class="text-primary"><i class="bi bi-box-arrow-in-right me-1"></i>Check In</span>';
                            }
                            
                            let typeText = 'Card/Other';
                            if (log.type == 1) typeText = 'Fingerprint';
                            else if (log.type == 0) typeText = 'Password';

                            // Format Date
                            const dateObj = new Date(log.timestamp);
                            const timeStr = dateObj.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour24: true }); 
                            const dateStr = dateObj.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });

                            html += `
                                <tr>
                                    <td class="fw-bold ps-4">${timeStr} <small class="text-muted ms-1">${dateStr}</small></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="fw-bold text-dark">${userName}</span>
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
                    
                    // Update Count in Widget
                    const countEl = document.getElementById('logsCount');
                    if(countEl) {
                        countEl.innerText = data.count;
                    }

                })
                .catch(error => console.error('Error fetching logs:', error));
        }, 5000); // 5000ms = 5 seconds
    </script>
</body>
</html>