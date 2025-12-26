<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Access - @yield('title', 'Dashboard')</title>
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
                <a href="{{ route('zk.dashboard') }}" class="list-group-item list-group-item-action bg-transparent {{ Request::routeIs('zk.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
                <a href="{{ route('zk.users.manager') }}" class="list-group-item list-group-item-action bg-transparent {{ Request::routeIs('zk.users.manager') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Users
                </a>
                <a href="{{ route('zk.logs') }}" class="list-group-item list-group-item-action bg-transparent {{ Request::routeIs('zk.logs') ? 'active' : '' }}">
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

                @yield('content')

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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle Sidebar
        document.getElementById("sidebarToggle").addEventListener("click", function () {
            document.body.classList.toggle("sb-sidenav-toggled");
        });

        // ==========================
        //  GLOBAL NOTIFICATION LOGIC
        // ==========================

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

        // We need 'users' variable to map IDs to Names effectively.
        // Since this layout is global, we might not always have '$users' passed from controller.
        // We will fetch User Map once via API or rely on what's available.
        // For simplicity in this demo, we assume the page might have a 'userMap' defined if available, 
        // or we just show Badge ID. Ideally, we should fetch users via API for notifications to work perfectly everywhere.
        
        let globalUserMap = {};
        
        // Let's try to fetch recent logs every 5 seconds globally to check for notifications
        let globalLastTimestamp = null;

        setInterval(function() {
            // We use the logs API just for checking new stuff.
            // We don't filter here, just get latest.
            fetch("{{ route('zk.api.logs') }}") 
                .then(response => response.json())
                .then(data => {
                    if (data.data.length > 0) {
                        // Init timestamp
                        if (!globalLastTimestamp) {
                            globalLastTimestamp = data.data[0].timestamp;
                            return;
                        }

                        // Check new logs
                        const newLogs = data.data.filter(log => log.timestamp > globalLastTimestamp);
                        
                        if (newLogs.length > 0) {
                            [...newLogs].reverse().forEach(log => {
                                // Try to find name from API object, else Map, else ID
                                const userName = (log.user && log.user.name) ? log.user.name : (globalUserMap[log.user_id] || "User " + log.user_id);
                                const stateText = log.state == 1 ? "Checked Out" : "Checked In";
                                showToast(userName, stateText);
                            });
                            
                            globalLastTimestamp = data.data[0].timestamp;
                        }
                    }
                    
                    // Note: If we are on the Logs Page, that page has its OWN poller for Table updates.
                    // This global one is just for toasts. 
                    // To avoid double fetching, we could coordinate, but for now 2 requests every 5s is fine.
                    
                })
                .catch(e => console.error("Notification Poll Error", e));
        }, 5000);

    </script>
    
    @yield('scripts')
</body>
</html>
