@extends('layouts.zk')

@section('title', 'Dashboard')

@section('content')
<!-- Stats Overview -->
<div class="row mb-5 g-4">
    <div class="col-md-3">
        <div class="card stat-card h-100 border-0 border-start border-4 border-info shadow-sm">
            <div class="d-flex align-items-center mb-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info mb-0 me-3 rounded-circle">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div>
                     <h6 class="text-uppercase text-muted small fw-bold mb-1 ls-1">Total Students</h6>
                     <h2 class="fw-bold mb-0 text-dark">{{ $totalStudents ?? 0 }}</h2>
                </div>
            </div>
            <div class="mt-auto">
                 <small class="text-muted small">from external API</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card h-100 border-0 border-start border-4 border-primary shadow-sm">
            <div class="d-flex align-items-center mb-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary mb-0 me-3 rounded-circle">
                    <i class="bi bi-people-fill"></i>
                </div>
                <div>
                     <h6 class="text-uppercase text-muted small fw-bold mb-1 ls-1">Device Users</h6>
                     <h2 class="fw-bold mb-0 text-dark">{{ $usersCount ?? 0 }}</h2>
                </div>
            </div>
            <div class="mt-auto">
                 <small class="text-success fw-medium"><i class="bi bi-arrow-up-short"></i> Active</small> <span class="text-muted small">on machine</span>
            </div>
        </div>
    </div>
    <div class="col-md-3">
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
    <div class="col-md-3">
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

<!-- Attendance Notification Modal -->
<div class="modal fade" id="attendanceModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content overflow-hidden border-0 shadow-lg" style="border-radius: 24px;">
      <div class="modal-body p-0">
        <div class="row g-0">
          <!-- Left Visual Side -->
          <div id="attendanceModalSide" class="col-md-5 d-flex flex-column align-items-center justify-content-center text-white py-5 px-4" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); min-height: 400px;">
             <div class="avatar-container mb-4 pulse-animation">
                <div id="attendanceAvatar" class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center shadow-lg" style="width: 160px; height: 160px; font-size: 64px; font-weight: 800;">
                    N
                </div>
             </div>
             <h2 id="attendanceStatusLabel" class="fw-bold text-uppercase ls-2 mb-0">CHECKED IN</h2>
          </div>
          <!-- Right Content Side -->
          <div class="col-md-7 d-flex flex-column justify-content-center py-5 px-5 bg-white">
            <div class="mb-4">
                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill mb-3" id="attendanceTimeLabel">JUST NOW</span>
                <h1 class="display-4 fw-800 text-dark mb-2" id="attendanceName" style="letter-spacing: -1px;">Nishad Hossain</h1>
                <p class="lead text-muted mb-0" id="attendanceMessage">Successfully logged your attendance on Smart Access system.</p>
            </div>
            
            <hr class="my-4 opacity-10">
            
            <div class="d-flex align-items-center mb-4">
                <div class="flex-shrink-0 me-3">
                    <div class="bg-light rounded-3 p-3 text-primary">
                        <i class="bi bi-clock-fill fs-3"></i>
                    </div>
                </div>
                <div>
                    <h6 class="text-muted small fw-bold text-uppercase mb-1">Time Captured</h6>
                    <h4 class="text-dark fw-bold mb-0" id="attendanceTimeStamp">12:59:09 PM</h4>
                </div>
            </div>

            <button type="button" class="btn btn-dark btn-lg rounded-pill px-5 fw-bold shadow-sm" data-bs-dismiss="modal">Dismiss</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Notification Sound -->
<audio id="attendanceSound" src="{{ asset('sounds/notification.mp3') }}" preload="auto"></audio>
@endsection
@section('scripts')
<script>
    // WebSocket listener for real-time attendance notifications
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.Echo) {
            console.warn('⚠️ Laravel Echo not found. Ensure assets are built.');
            return;
        }

        const modalEl = document.getElementById('attendanceModal');
        const modalSide = document.getElementById('attendanceModalSide');
        const avatarEl = document.getElementById('attendanceAvatar');
        const statusLabel = document.getElementById('attendanceStatusLabel');
        const nameEl = document.getElementById('attendanceName');
        const timeStampEl = document.getElementById('attendanceTimeStamp');
        const soundEl = document.getElementById('attendanceSound');
        const attendanceModal = new bootstrap.Modal(modalEl);

        window.Echo.channel('attendance-updates')
            .listen('AttendanceSynced', (e) => {
                if (e.newLogs && e.newLogs.length) {
                    e.newLogs.forEach(log => {
                        const userName = (log.user && log.user.name) ? log.user.name : "User " + log.user_id;
                        const isCheckIn = log.state != 1;
                        const stateText = isCheckIn ? 'Checked In' : 'Checked Out';
                        const timeStr = new Date(log.timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                        
                        // Update UI
                        nameEl.innerText = userName;
                        statusLabel.innerText = stateText.toUpperCase();
                        timeStampEl.innerText = timeStr;
                        avatarEl.innerText = userName.charAt(0).toUpperCase();

                        if (isCheckIn) {
                            modalSide.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
                            avatarEl.className = 'rounded-circle bg-white text-success d-flex align-items-center justify-content-center shadow-lg';
                        } else {
                            modalSide.style.background = 'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)';
                            avatarEl.className = 'rounded-circle bg-white text-primary d-flex align-items-center justify-content-center shadow-lg';
                        }

                        // Speech synthesis announcement
                        const announcement = `${userName} just ${stateText.toLowerCase()}`;
                        const utterance = new SpeechSynthesisUtterance(announcement);
                        utterance.rate = 0.9; // Slightly slower for clarity
                        utterance.pitch = 1.1; 
                        window.speechSynthesis.speak(utterance);

                        // Sound Effect
                        if (soundEl) {
                            soundEl.currentTime = 0;
                            soundEl.play().catch(err => console.warn('Sound play failed', err));
                        }

                        attendanceModal.show();
                    });

                    // Refresh DASHBOARD count after handling new logs (Single API call for the whole batch)
                    fetch("{{ route('zk.api.logs') }}?filter=today")
                        .then(r => r.json())
                        .then(d => {
                            const cnt = document.getElementById('dashboardLogCount');
                            if (cnt) cnt.innerText = d.count;
                        });
                }
            });
    });
</script>
@endsection
