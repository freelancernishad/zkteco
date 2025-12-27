@extends('layouts.zk')

@section('title', 'Student Directory')

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
            <div class="card-body p-0">
                <div class="p-4 bg-white border-bottom d-flex flex-wrap justify-content-between align-items-center gap-4">
                    <div>
                        <h4 class="fw-800 mb-1 text-dark">Student Directory</h4>
                        <p class="text-muted small mb-0">Total Students: <span class="badge bg-primary bg-opacity-10 text-primary" id="totalCountBadge">{{ count($students) }}</span></p>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3 flex-grow-1 justify-content-end">
                        <div class="search-container flex-grow-1" style="max-width: 400px;">
                            <div class="input-group input-group-sm search-group shadow-sm border rounded-pill overflow-hidden transition-all">
                                <span class="input-group-text bg-white border-0 text-primary ps-3"><i class="bi bi-search py-1"></i></span>
                                <input type="text" id="studentSearchInput" class="form-control border-0 bg-white small fw-medium py-2" placeholder="Search by name, English name or Badge ID..." style="outline: none; box-shadow: none;">
                            </div>
                        </div>
                        
                        <div class="filter-container">
                            <div class="input-group input-group-sm filter-group shadow-sm border rounded-pill overflow-hidden">
                                <label class="input-group-text bg-light border-0 text-muted small fw-bold px-3" for="classFilter">CLASS</label>
                                <select id="classFilter" class="form-select border-0 bg-white small fw-bold text-dark py-2" style="min-width: 130px; outline: none; box-shadow: none;">
                                    <option value="">All Classes</option>
                                    @foreach($classes as $class)
                                    <option value="{{ $class }}">{{ $class }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Action Bar (Controlled by JS) -->
                <div id="bulkSyncBar" class="px-4 py-3 bg-primary bg-opacity-10 border-bottom d-flex align-items-center justify-content-between d-none transition-all">
                    <div class="d-flex align-items-center text-primary fw-bold">
                        <i class="bi bi-info-circle-fill me-2 anim-pulse"></i>
                        <span>Ready to sync Class: <span id="selectedClassName" class="text-uppercase mx-1"></span> (<span id="filteredCount">0</span> students)</span>
                    </div>
                    <form action="{{ route('zk.students.sync.class') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="class" id="bulkSyncClassInput">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 shadow-sm border-0 fw-bold transition-transform hover-scale" onclick="return confirm('Create all students in this class as users on the device?')">
                            <i class="bi bi-person-plus-fill me-1"></i> CREATE ALL USERS
                        </button>
                    </form>
                </div>

                @if(isset($error))
                <div class="p-5 text-center">
                    <div class="display-1 text-danger mb-4"><i class="bi bi-exclamation-triangle"></i></div>
                    <h5 class="text-dark fw-bold">{{ $error }}</h5>
                    <p class="text-muted">Please try again later or check your internet connection.</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0" id="studentTable">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="ps-4 text-uppercase small text-muted fw-bold py-3" style="width: 80px;">Badge ID</th>
                                <th class="text-uppercase small text-muted fw-bold py-3">Student Full Name</th>
                                <th class="text-uppercase small text-muted fw-bold py-3">Class</th>
                                <th class="text-uppercase small text-muted fw-bold py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                            <tr class="student-row" data-class="{{ $student['StudentClass'] }}">
                                <td class="ps-4">
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold student-id">#{{ $student['id'] }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 text-primary fw-bold" style="width: 45px; height: 45px; font-size: 1.1rem;">
                                            {{ strtoupper(substr($student['StudentNameEn'] ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold text-dark student-name">{{ $student['StudentName'] }} ({{ $student['StudentClass'] }})</h6>
                                            <p class="mb-0 text-muted small text-capitalize student-name-en">{{ $student['StudentNameEn'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">{{ $student['StudentClass'] }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('zk.students.sync', $student['id']) }}" 
                                           class="btn btn-sm btn-outline-primary rounded-pill px-3 border-0 bg-primary bg-opacity-10 text-primary fw-bold transition-all hover-translate-y"
                                           onclick="return confirm('Create user for {{ $student['StudentName'] }} on the device?')">
                                            <i class="bi bi-person-plus-fill me-1"></i> Create User
                                        </a>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-light border-0 dropdown-toggle no-caret p-2 px-3 rounded-pill" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm br-12">
                                                <li><a class="dropdown-item py-2 small" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                                                <li><a class="dropdown-item py-2 small" href="#"><i class="bi bi-calendar-check me-2"></i>Attendance</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted opacity-50 mb-3"><i class="bi bi-search" style="font-size: 3rem;"></i></div>
                                    <h6 class="text-muted">No students found for the selected criteria.</h6>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearchInput');
    const classFilter = document.getElementById('classFilter');
    const studentRows = document.querySelectorAll('.student-row');
    const bulkSyncBar = document.getElementById('bulkSyncBar');
    const selectedClassName = document.getElementById('selectedClassName');
    const bulkSyncClassInput = document.getElementById('bulkSyncClassInput');
    const filteredCount = document.getElementById('filteredCount');

    function applyFilters() {
        const query = searchInput.value.toLowerCase().trim();
        const selectedClass = classFilter.value;
        let count = 0;

        studentRows.forEach(row => {
            const name = row.querySelector('.student-name').textContent.toLowerCase();
            const nameEn = row.querySelector('.student-name-en').textContent.toLowerCase();
            const id = row.querySelector('.student-id').textContent.toLowerCase();
            const rowClass = row.getAttribute('data-class');

            const matchesSearch = !query || name.includes(query) || nameEn.includes(query) || id.includes(query);
            const matchesClass = !selectedClass || rowClass === selectedClass;

            if (matchesSearch && matchesClass) {
                row.style.display = '';
                count++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update UI for bulk sync bar
        if (selectedClass) {
            bulkSyncBar.classList.remove('d-none');
            selectedClassName.textContent = selectedClass;
            bulkSyncClassInput.value = selectedClass;
            filteredCount.textContent = count;
        } else {
            bulkSyncBar.classList.add('d-none');
        }
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);
    if (classFilter) classFilter.addEventListener('change', applyFilters);
});
</script>

<style>
    .fw-800 { font-weight: 800; }
    .ls-1 { letter-spacing: 1px; }
    .br-12 { border-radius: 12px; }
    .no-caret::after { display: none; }
    
    .search-group:focus-within {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
    }
    
    .transition-all { transition: all 0.3s ease; }
    .hover-scale:hover { transform: scale(1.05); }
    .hover-translate-y:hover { transform: translateY(-2px); }
    
    .anim-pulse {
        animation: pulse-blue 2s infinite;
    }
    
    @keyframes pulse-blue {
        0% { transform: scale(0.95); opacity: 0.7; }
        70% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(0.95); opacity: 0.7; }
    }
</style>
@endsection
