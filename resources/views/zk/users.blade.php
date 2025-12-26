@extends('layouts.zk')

@section('title', 'User Management')

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">User Management</h5>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus-lg me-2"></i>Add User
            </button>
        </div>
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

@endsection

@section('scripts')
<script>
    // Populate global map for notifications (if needed)
    @if(isset($users))
        @foreach($users as $u)
            globalUserMap["{{ $u['userid'] }}"] = "{{ $u['name'] }}";
        @endforeach
    @endif
</script>
@endsection
