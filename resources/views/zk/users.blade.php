@extends('layouts.zk')

@section('title', 'User Management')

@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 py-4 px-4 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="fw-bold mb-1">User Management</h5>
            <p class="text-muted small mb-0">Manage registered users and sync with device.</p>
        </div>
        <button class="btn btn-primary btn-sm px-4 rounded-pill" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-plus-lg me-2"></i>Add User
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
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
                                    <button class="btn btn-sm btn-outline-info" onclick="checkFingerprint('{{ $user->uid }}')">
                                        <i class="bi bi-fingerprint me-1"></i> Check Fingerprint
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
</script>

<style>
    .fade-out {
        opacity: 0;
        transform: translateX(20px);
        transition: all 0.5s ease;
    }
</style>
@endsection
