@extends('layouts.zk')

@section('title', 'User Management')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-4 px-4 d-flex justify-content-between align-items-center sticky-top shadow-sm" style="top: 72px; z-index: 900;">
        <div>
            <h5 class="fw-bold mb-1">User Management</h5>
            <p class="text-muted small mb-0">Manage registered users and sync with device.</p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <div class="input-group input-group-sm" style="width: 250px;">
                <span class="input-group-text bg-white border-end-0 text-muted">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" id="userListSearch" class="form-control border-start-0 ps-0" placeholder="Search name or ID...">
            </div>
            <button class="btn btn-primary btn-sm px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus-lg me-2"></i>Add User
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">UID</th>
                        <th>User ID (Badge)</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Fingerprint Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($users) && count($users) > 0)
                        @foreach($users as $user)
                        <tr id="user-row-{{ $user->uid }}">
                            <td class="fw-bold text-secondary ps-4">#{{ $user->uid }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $user->userid }}</span></td>
                            <td class="fw-bold" id="user-name-{{ $user->uid }}">{{ $user->name }}</td>
                            <td>
                                @if($user->role == 14) 
                                    <span class="badge badge-admin">Admin</span>
                                @else 
                                    <span class="badge badge-user">User</span>
                                @endif
                            </td>
                            <td>
                                <div id="fp-status-{{ $user->uid }}" class="small fw-bold">
                                    @if($user->fingerprint_status == 'added')
                                        <span class="text-success"><i class="bi bi-check-circle-fill"></i> Fingerprint Added</span>
                                    @else
                                        <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Not Added</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <button class="btn btn-sm btn-outline-success" onclick="registerFingerprint('{{ $user->uid }}', '{{ $user->userid }}', '{{ $user->name }}')">
                                        <i class="bi bi-plus-circle me-1"></i> Add Finger
                                    </button>
                                    <button class="btn btn-sm btn-outline-info" onclick="checkFingerprint('{{ $user->uid }}')">
                                        <i class="bi bi-fingerprint me-1"></i> Check
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning" onclick="editUser('{{ $user->uid }}', '{{ $user->name }}')">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button" id="delete-btn-{{ $user->uid }}" class="btn btn-sm btn-outline-danger" onclick="deleteUser('{{ $user->uid }}', '{{ $user->name }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                    No users found in database. Please sync with device.
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
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

<!-- Modal: Edit User -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit User Name on Device</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editUid">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" id="editNameInput" class="form-control" placeholder="e.g. John Doe" required>
                </div>
                <div class="alert alert-warning small mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i> Changing the name here will update it on the ZKTeco device immediately.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="saveEditBtn" class="btn btn-primary" onclick="saveUserEdit()">Update Name</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Register Fingerprint Instructions -->
<div class="modal fade" id="registerFingerprintModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content overflow-hidden border-0 shadow">
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-fingerprint me-2"></i>Register Fingerprint</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="display-6 fw-bold text-success mb-1" id="regUserName">User Name</div>
                    <div class="badge bg-light text-dark border px-3 py-2">Badge ID: <span id="regBadgeId" class="fw-bold">0000</span></div>
                </div>

                <div class="instruction-steps">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Device Enrollment Steps:</h6>
                    <div class="d-flex mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3 shrink-0" style="min-width: 30px; height: 30px;">1</div>
                        <div class="text-muted small">Go to the **ZKTeco Device** and press **M/OK** to enter Menu.</div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3 shrink-0" style="min-width: 30px; height: 30px;">2</div>
                        <div class="text-muted small">Navigate to **User Mgt** → **All Users**.</div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3 shrink-0" style="min-width: 30px; height: 30px;">3</div>
                        <div class="text-muted small">Select user with Badge ID <b class="text-dark" id="stepBadgeId">0000</b> and press **M/OK**.</div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3 shrink-0" style="min-width: 30px; height: 30px;">4</div>
                        <div class="text-muted small">Select **Edit** → **FP** (or Register Fingerprint).</div>
                    </div>
                    <div class="d-flex mb-0">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center me-3 shrink-0" style="min-width: 30px; height: 30px;">5</div>
                        <div class="text-muted small">Press your finger **3 times** on the sensor until the device saves.</div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-grid">
                    <button type="button" class="btn btn-outline-primary fw-bold py-2" id="checkFpInModalBtn">
                        <i class="bi bi-arrow-repeat me-2"></i> Verify Registration Status
                    </button>
                    <div id="modalFpStatus" class="text-center mt-2 small fw-bold"></div>
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary px-4 rounded-pill" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function checkFingerprint(uid) {
        const statusDiv = document.getElementById(`fp-status-${uid}`);
        statusDiv.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Checking...';
        
        fetch(`/zk/user/fingerprint/${uid}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'added') {
                    statusDiv.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill"></i> Fingerprint Added</span>';
                } else if (data.status === 'not_added') {
                    statusDiv.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle-fill"></i> Not Added</span>';
                } else {
                    statusDiv.innerHTML = `<span class="text-warning">${data.message}</span>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statusDiv.innerHTML = '<span class="text-danger">Error checking</span>';
            });
    }

    function deleteUser(uid, name) {
        // Removing native confirm as requested.
        // We will show a toast when starting or just proceed.
        
        const btn = document.getElementById(`delete-btn-${uid}`);
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span>';
        btn.disabled = true;

        showToast(`Deleting ${name}...`, 'Processing', 'info');

        fetch(`/zk/user/delete/${uid}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const row = document.getElementById(`user-row-${uid}`);
                if (row) {
                    row.classList.add('fade-out');
                    setTimeout(() => row.remove(), 500);
                }
                showToast(data.message, 'Deleted', 'success');
            } else {
                btn.innerHTML = originalContent;
                btn.disabled = false;
                showToast(data.message, 'Error', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.innerHTML = originalContent;
            btn.disabled = false;
            showToast('Failed to connect to server.', 'Error', 'error');
        });
    }

    let editModalObj = null;

    function editUser(uid, currentName) {
        if (!editModalObj) {
            editModalObj = new bootstrap.Modal(document.getElementById('editUserModal'));
        }
        document.getElementById('editUid').value = uid;
        document.getElementById('editNameInput').value = currentName;
        editModalObj.show();
    }

    function saveUserEdit() {
        const uid = document.getElementById('editUid').value;
        const newName = document.getElementById('editNameInput').value;
        const saveBtn = document.getElementById('saveEditBtn');
        const originalContent = saveBtn.innerHTML;

        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Updating...';
        saveBtn.disabled = true;

        fetch('/zk/user/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                uid: uid,
                name: newName
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById(`user-name-${uid}`).innerText = data.name;
                showToast(data.message, 'Updated', 'success');
                editModalObj.hide();
            } else {
                showToast(data.message, 'Error', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Failed to connect to server.', 'Error', 'error');
        })
        .finally(() => {
            saveBtn.innerHTML = originalContent;
            saveBtn.disabled = false;
        });
    }

    let registerModalObj = null;
    let currentRegUid = null;

    function registerFingerprint(uid, userid, name) {
        if (!registerModalObj) {
            registerModalObj = new bootstrap.Modal(document.getElementById('registerFingerprintModal'));
        }
        
        currentRegUid = uid;
        document.getElementById('regUserName').textContent = name;
        document.getElementById('regBadgeId').textContent = userid;
        document.getElementById('stepBadgeId').textContent = userid;
        document.getElementById('modalFpStatus').innerHTML = '';
        
        registerModalObj.show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Search Functionality
        const searchInput = document.getElementById('userListSearch');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const query = this.value.toLowerCase().trim();
                const tableRows = document.querySelectorAll('tbody tr:not(.text-center)');
                
                tableRows.forEach(row => {
                    const name = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    const badge = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    
                    if (name.includes(query) || badge.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }

        const checkBtnInModal = document.getElementById('checkFpInModalBtn');
        if (checkBtnInModal) {
            checkBtnInModal.addEventListener('click', function() {
                if (!currentRegUid) return;
                
                const originalContent = this.innerHTML;
                this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Verifying...';
                this.disabled = true;
                
                checkFingerprint(currentRegUid); // Use existing logic to update main table
                
                // Also update modal UI
                fetch(`/zk/user/fingerprint/${currentRegUid}`)
                    .then(response => response.json())
                    .then(data => {
                        const statusDiv = document.getElementById('modalFpStatus');
                        if (data.status === 'added') {
                            statusDiv.innerHTML = '<span class="text-success fs-6"><i class="bi bi-check-circle-fill"></i> Success! Fingerprint has been added.</span>';
                        } else {
                            statusDiv.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle-fill"></i> Still not detected. Please follow the steps above.</span>';
                        }
                    })
                    .finally(() => {
                        this.innerHTML = originalContent;
                        this.disabled = false;
                    });
            });
        }
    });
</script>

<style>
    .fade-out {
        opacity: 0;
        transform: translateX(20px);
        transition: all 0.5s ease;
    }

    /* Sticky Header refined stacking */
    .card-header.sticky-top {
        top: 72px; 
        z-index: 990;
        background-color: #fff;
    }

    /* Sticky Table Header (Page Level) */
    .table thead th {
        position: sticky;
        top: 160px; /* Navbar (72px) + Card Header (~88px) */
        background-color: #f8f9fa !important;
        z-index: 980;
        border-bottom: 2px solid #dee2e6 !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
</style>
@endsection
