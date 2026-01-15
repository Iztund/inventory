@extends('layouts.admin')

@section('title', 'Edit User: ' . $user->username)

@section('content')
<div class="container-fluid py-5 px-lg-5" style="background-color: #f8fafc; min-height: 100vh;">

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.users.index') }}" class="btn btn-white shadow-sm rounded-3 me-3 border-0" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: white;">
                <i class="fas fa-chevron-left text-muted"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Modify User Account</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small text-muted bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none">Manage Users</a></li>
                        <li class="breadcrumb-item active">Edit: {{ $user->username }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <div>
            <button type="submit" form="editUserForm" class="btn btn-dark px-4 py-2 rounded-3 fw-medium shadow-sm">
                <i class="fas fa-save me-2"></i>Update Account
            </button>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4">
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white rounded-4 shadow-sm p-5 border-0 mb-4">
                <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST" id="editUserForm">
                    @csrf
                    @method('PUT')

                    <h6 class="text-uppercase text-primary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">Identity & Access</h6>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Full Name</label>
                            <input type="text" name="full_name" class="form-control border-0 bg-light @error('full_name') is-invalid @enderror" 
                                   value="{{ old('full_name', $user->profile->full_name ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Username</label>
                            <input type="text" name="username" class="form-control border-0 bg-light @error('username') is-invalid @enderror" 
                                   value="{{ old('username', $user->username) }}" required>
                        </div>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Email Address</label>
                            <input type="email" name="email" class="form-control border-0 bg-light @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $user->email) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Account Status</label>
                            <select name="status" class="form-select border-0 bg-light">
                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <h6 class="text-uppercase text-primary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">Organization & Role</h6>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-dark small">System Role</label>
                        <select name="role_id" class="form-select border-0 bg-light">
                            @foreach($roles as $role)
                                <option value="{{ $role->role_id }}" {{ old('role_id', $user->role_id) == $role->role_id ? 'selected' : '' }}>
                                    {{ $role->role_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="p-4 rounded-4 border bg-white mb-4 shadow-sm">
                        <p class="small text-muted mb-3 fw-medium text-uppercase" style="font-size: 0.60rem;">Primary Affiliation (Choose One)</p>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label x-small text-muted">FACULTY</label>
                                <select name="faculty_id" class="form-select border-light bg-light-subtle text-truncate">
                                    <option value="">None</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->faculty_id }}" {{ old('faculty_id', $user->faculty_id) == $faculty->faculty_id ? 'selected' : '' }}>
                                            {{ $faculty->faculty_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label x-small text-muted">INSTITUTE</label>
                                <select name="institute_id" class="form-select border-light bg-light-subtle text-truncate">
                                    <option value="">None</option>
                                    @foreach($institutes as $institute)
                                        <option value="{{ $institute->institute_id }}" {{ old('institute_id', $user->institute_id) == $institute->institute_id ? 'selected' : '' }}>
                                            {{ $institute->institute_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label x-small text-muted">OFFICE</label>
                                <select name="office_id" class="form-select border-light bg-light-subtle text-truncate">
                                    <option value="">None</option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->office_id }}" {{ old('office_id', $user->office_id) == $office->office_id ? 'selected' : '' }}>
                                            {{ $office->office_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Department (Optional)</label>
                            <select name="dept_id" class="form-select border-0 bg-light">
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->dept_id }}" {{ old('dept_id', $user->dept_id) == $department->dept_id ? 'selected' : '' }}>
                                        {{ $department->dept_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold text-dark small">Unit (Optional)</label>
                            <select name="unit_id" class="form-select border-0 bg-light">
                                <option value="">Select Unit</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->unit_id }}" {{ old('unit_id', $user->unit_id) == $unit->unit_id ? 'selected' : '' }}>
                                        {{ $unit->unit_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h6 class="text-uppercase text-primary fw-bold mb-4 mt-5" style="font-size: 0.75rem; letter-spacing: 0.1em;">Contact Information</h6>
                    <div class="row g-4">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-dark small">Phone Number</label>
                            <input type="text" name="phone" class="form-control border-0 bg-light" value="{{ old('phone', $user->profile->phone ?? '') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold text-dark small">Address</label>
                            <textarea name="address" class="form-control border-0 bg-light" rows="2">{{ old('address', $user->profile->address ?? '') }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="bg-white rounded-4 shadow-sm border-0 overflow-hidden mb-4">
                <div class="p-4 bg-warning bg-opacity-10 border-bottom border-warning border-opacity-25">
                    <h6 class="fw-bold text-warning-emphasis mb-1 small text-uppercase"><i class="fas fa-shield-alt me-2"></i>Security Action</h6>
                </div>
                <div class="p-4">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="reset_password" name="reset_password" value="1" form="editUserForm">
                        <label class="form-check-label fw-bold text-dark" for="reset_password">Reset to Default Password</label>
                    </div>
                    <p class="text-muted small mb-0">
                        Restores access using <code class="fw-bold">{{ $user->username }}@2025</code>. 
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-4 shadow-sm border-0 p-4">
                <h6 class="fw-bold text-dark mb-3 small text-uppercase">Account Details</h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Registered</span>
                    <span class="text-dark small fw-medium">{{ $user->created_at->format('M d, Y') }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted small">Modified</span>
                    <span class="text-dark small fw-medium">{{ $user->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control, .form-select { padding: 0.75rem 1rem; border-radius: 10px; transition: all 0.2s ease; }
    .form-control:focus, .form-select:focus { background-color: #fff !important; box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1); border: 1px solid #0d6efd !important; }
    .x-small { font-size: 0.65rem; font-weight: 800; }
    .bg-light-subtle { background-color: #f1f5f9 !important; }
</style>
@endsection