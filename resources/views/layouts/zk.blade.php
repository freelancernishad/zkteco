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
        :root {
            --primary-color: #4f46e5;
            --primary-hover: #4338ca;
            --sidebar-bg: #1e1e2d; 
            --sidebar-width: 280px;
        }

        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f4f6f9; 
            color: #333;
            overflow-x: hidden; 
        }
        
        #wrapper { display: flex; width: 100%; }
        
        /* Sidebar Styling */
        #sidebar-wrapper { 
            min-height: 100vh; 
            margin-left: calc(-1 * var(--sidebar-width)); 
            width: var(--sidebar-width); 
            background-color: var(--sidebar-bg);
            transition: margin .3s ease-in-out; 
            z-index: 1000;
        }
        
        #sidebar-wrapper .sidebar-heading { 
            padding: 1.5rem; 
            font-size: 1.25rem; 
            font-weight: 700; 
            color: #fff; 
            border-bottom: 1px solid rgba(255,255,255,0.05);
            background: rgba(0,0,0,0.1);
            letter-spacing: 0.5px;
        }
        
        #sidebar-wrapper .list-group { width: var(--sidebar-width); padding: 1rem 0; }
        
        #sidebar-wrapper .list-group-item { 
            border: none; 
            padding: 14px 24px; 
            color: #a2a3b7; 
            background: transparent; 
            font-weight: 500; 
            font-size: 0.95rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        
        #sidebar-wrapper .list-group-item:hover { 
            background-color: rgba(255,255,255,0.03); 
            color: #fff; 
        }
        
        #sidebar-wrapper .list-group-item.active { 
            background-color: rgba(79, 70, 229, 0.1); 
            color: #7269ef; 
            border-left: 3px solid #7269ef;
        }
        
        #sidebar-wrapper .list-group-item i { 
            width: 24px; 
            display: inline-block; 
            text-align: center; 
            margin-right: 12px; 
            font-size: 1.1rem;
        }

        /* Content Area */
        #page-content-wrapper { 
            min-width: 100vw; 
            width: 100%;
            transition: margin .3s ease-in-out; 
        }
        
        /* Toggled State */
        body.sb-sidenav-toggled #wrapper #sidebar-wrapper { margin-left: 0; }
        
        @media (min-width: 768px) {
            #sidebar-wrapper { margin-left: 0; }
            #page-content-wrapper { min-width: 0; width: 100%; }
            body.sb-sidenav-toggled #wrapper #sidebar-wrapper { margin-left: calc(-1 * var(--sidebar-width)); }
        }

        /* Cards */
        .card { 
            border: none; 
            border-radius: 10px; 
            background: #ffffff;
            box-shadow: 0 0 20px 0 rgba(76, 87, 125, 0.02);
            margin-bottom: 24px; 
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .stat-card {
            border-radius: 12px;
            padding: 24px;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .stat-icon { 
            width: 54px; 
            height: 54px; 
            border-radius: 10px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 26px; 
            margin-bottom: 20px; 
            transition: all 0.3s;
        }

        /* Navbar */
        .navbar { 
            background: #fff; 
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); 
            padding: 1rem 1.5rem; 
        }
        
        .navbar .btn-outline-primary {
            border-color: #eef0f8;
            color: #7e8299;
            background: #f5f8fa;
        }
        
        .navbar .btn-outline-primary:hover {
            background: var(--primary-color);
            color: #fff;
            border-color: var(--primary-color);
        }

        /* Primary Button Override */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }

        /* Table */
        .table thead th { 
            background-color: #f9f9f9; 
            font-weight: 600; 
            text-transform: uppercase; 
            font-size: 0.75rem; 
            letter-spacing: 0.05em; 
            color: #b5b5c3; 
            padding: 14px 20px; 
            border-bottom: 1px solid #eff2f5; 
        }
        
        .table tbody td { 
            padding: 18px 20px; 
            vertical-align: middle; 
            border-bottom: 1px dashed #eff2f5;
            color: #464e5f;
            font-weight: 500;
        }

        /* Badges */
        .badge { padding: 8px 12px; border-radius: 6px; font-weight: 600; font-size: 0.75rem; }
    </style>

</head>
<body>

    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <div class="sidebar-heading">
                <!-- Branding -->
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-gradient bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; font-size: 20px;">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <span>Smart Access</span>
                </div>
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
                <a href="{{ route('zk.connect') }}" class="list-group-item list-group-item-action bg-transparent {{ Request::routeIs('zk.connect') ? 'active' : '' }}">
                    <i class="bi bi-router"></i> Device Info
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
                                    <i class="bi bi-wifi me-2"></i>Device IP: {{ env('ZK_DEVICE_IP', '192.168.0.201') }}
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
