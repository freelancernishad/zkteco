@extends('layouts.zk')

@section('title', 'Staff Directory')

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-0 shadow-sm" style="border-radius: 15px;">
            <div class="card-body p-0">
                <div class="p-4 bg-white border-bottom d-flex flex-wrap justify-content-between align-items-center gap-4 sticky-top shadow-sm" style="top: 72px; z-index: 1010;">
                    <div>
                        <h4 class="fw-800 mb-1 text-dark">Staff Directory</h4>
                        <p class="text-muted small mb-0">Total Staffs: <span class="badge bg-primary bg-opacity-10 text-primary" id="totalCountBadge">...</span></p>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3 flex-grow-1 justify-content-end">
                        <div class="search-container flex-grow-1" style="max-width: 400px;">
                            <div class="input-group input-group-sm search-group shadow-sm border rounded-pill overflow-hidden transition-all">
                                <span class="input-group-text bg-white border-0 text-primary ps-3"><i class="bi bi-search py-1"></i></span>
                                <input type="text" id="staffSearchInput" class="form-control border-0 bg-white small fw-medium py-2" placeholder="Search by name or Badge ID..." style="outline: none; box-shadow: none;">
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($error))
                <div class="p-5 text-center">
                    <div class="display-1 text-danger mb-4"><i class="bi bi-exclamation-triangle"></i></div>
                    <h5 class="text-dark fw-bold">{{ $error }}</h5>
                    <p class="text-muted">Please try again later or check your internet connection.</p>
                </div>
                @else
                <div class="">
                    <table class="table align-middle table-hover mb-0" id="staffTable">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="ps-4 text-uppercase small text-muted fw-bold py-3" style="width: 120px;">Badge ID</th>
                                <th class="text-uppercase small text-muted fw-bold py-3">Staff Full Name</th>
                                <th class="text-uppercase small text-muted fw-bold py-3 text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="staffTableBody">
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="spinner-border text-primary mb-3" role="status"></div>
                                    <h6 class="text-muted">Loading staffs...</h6>
                                </td>
                            </tr>
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
    const searchInput = document.getElementById('staffSearchInput');
    const tableBody = document.getElementById('staffTableBody');
    const totalCountBadge = document.getElementById('totalCountBadge');
    
    window.allStaffs = [];
    window.existingUserIds = [];

    // Load staffs via AJAX
    function loadStaffs() {
        fetch('/zk/api/staffs')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.allStaffs = data.staffs;
                    window.existingUserIds = data.existingUserIds.map(String);
                    totalCountBadge.textContent = window.allStaffs.length;
                    renderStaffs(window.allStaffs);
                } else {
                    tableBody.innerHTML = `<tr><td colspan="3" class="text-center py-5 text-danger">${data.message}</td></tr>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                tableBody.innerHTML = '<tr><td colspan="3" class="text-center py-5 text-danger">Failed to load staffs.</td></tr>';
            });
    }

    function renderStaffs(staffs) {
        if (staffs.length === 0) {
            tableBody.innerHTML = `<tr>
                                    <td colspan="3" class="text-center py-5">
                                        <div class="text-muted opacity-50 mb-3"><i class="bi bi-search" style="font-size: 3rem;"></i></div>
                                        <h6 class="text-muted">No staffs found.</h6>
                                    </td>
                                </tr>`;
            return;
        }

        tableBody.innerHTML = staffs.map(staff => {
            const displayId = staff.id_prefixed || ("999" + staff.id);
            const isRegistered = window.existingUserIds.includes(String(displayId));
            const statusHtml = isRegistered 
                ? `<span class="badge bg-success text-white fw-bold rounded-pill px-3 py-2 d-inline-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-1"></i> User Created on Device
                   </span>`
                : `<button type="button" 
                        class="btn btn-sm btn-outline-primary rounded-pill px-3 border-0 bg-primary bg-opacity-10 text-primary fw-bold transition-all hover-translate-y"
                        onclick="createStaffUser('${staff.id}', '${staff.TeacherName.replace(/'/g, "\\'")}')">
                        <i class="bi bi-person-plus-fill me-1"></i> Create User
                   </button>`;

            return `
                <tr class="staff-row">
                    <td class="ps-4">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary fw-bold staff-id">#${displayId}</span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3 text-primary fw-bold" style="width: 45px; height: 45px; font-size: 1.1rem;">
                                ${(staff.TeacherName || 'S').charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark staff-name">${staff.TeacherName}</h6>
                            </div>
                        </div>
                    </td>
                    <td class="text-end pe-4">
                        <div id="sync-status-${staff.id}" class="d-flex justify-content-end align-items-center gap-2">
                            <div id="status-inner-${staff.id}">
                                ${statusHtml}
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    function applyFilters() {
        const query = searchInput.value.toLowerCase().trim();
        
        const filtered = window.allStaffs.filter(staff => {
            const displayId = staff.id_prefixed || ("999" + staff.id);
            const matchesSearch = !query || 
                                staff.TeacherName.toLowerCase().includes(query) || 
                                String(displayId).includes(query) ||
                                String(staff.id).includes(query);
            return matchesSearch;
        });

        renderStaffs(filtered);
    }

    if (searchInput) searchInput.addEventListener('input', applyFilters);

    loadStaffs();
});

window.createStaffUser = function(staffId, staffName) {
    const container = document.getElementById(`status-inner-${staffId}`);
    const originalContent = container.innerHTML;
    
    container.innerHTML = `<span class="badge bg-primary bg-opacity-10 text-primary fw-bold rounded-pill px-3 py-2 d-inline-flex align-items-center">
                                <span class="spinner-border spinner-border-sm me-1" role="status"></span> Creating...
                           </span>`;

    fetch(`/zk/staffs/sync/${staffId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            if (window.existingUserIds) {
                window.existingUserIds.push(String(staffId));
            }
            container.innerHTML = `<span class="badge bg-success text-white fw-bold rounded-pill px-3 py-2 d-inline-flex align-items-center"><i class="bi bi-check-circle-fill me-1"></i> User Created on Device</span>`;
            showToast(data.message, 'Success', 'success');
        } else {
            container.innerHTML = originalContent;
            showToast(data.message, 'Error', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        container.innerHTML = originalContent;
        showToast('Failed to connect to server.', 'Connection Error', 'error');
    });
};
</script>

<style>
    .fw-800 { font-weight: 800; }
    .br-12 { border-radius: 12px; }
    
    .search-group:focus-within {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.15) !important;
    }
    
    .transition-all { transition: all 0.3s ease; }
    .hover-translate-y:hover { transform: translateY(-2px); }

    #staffTable thead th {
        position: sticky;
        top: 168px;
        background-color: #f8f9fa !important;
        z-index: 1000;
        border-bottom: 2px solid #dee2e6 !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }
</style>
@endsection
