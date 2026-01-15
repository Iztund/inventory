@extends('layouts.admin')

@section('title', 'Create New User')

@section('content')
<div class="container-fluid py-5 px-lg-5" style="background-color: #f8fafc; min-height: 100vh;">

    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.users.index') }}" 
               class="btn btn-white shadow-sm rounded-3 me-3 border-0" 
               style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: white;">
                <i class="fas fa-chevron-left text-muted" style="font-size: 0.9rem;"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">Create New User</h4>
                <span class="text-muted small">Set up a new account for the system</span>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-link text-decoration-none text-muted me-3 fw-medium">Cancel</a>
            <button type="submit" form="createUserForm" class="btn btn-primary px-4 py-2 rounded-3 fw-medium shadow-sm">
                <i class="fas fa-user-plus me-2"></i>Register User
            </button>
        </div>
    </div>

    @if ($errors->has('primary_affiliation_error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 px-4 py-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-3"></i>
                <div><strong>Configuration Error:</strong> {{ $errors->first('primary_affiliation_error') }}</div>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white rounded-4 shadow-sm p-5 border-0">
                <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                    @csrf

                    <section class="mb-5">
                        <h6 class="text-uppercase text-primary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">Account Identity</h6>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Full Name</label>
                            <input type="text" name="full_name" 
                                   class="form-control form-control-lg border-0 bg-light @error('full_name') is-invalid @enderror" 
                                   placeholder="Lastname Firstname M." value="{{ old('full_name') }}" required>
                            @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Username</label>
                                <input type="text" name="username" 
                                       class="form-control border-0 bg-light @error('username') is-invalid @enderror" 
                                       placeholder="unique_username" value="{{ old('username') }}" required>
                                @error('username') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Email Address</label>
                                <input type="email" name="email" 
                                       class="form-control border-0 bg-light @error('email') is-invalid @enderror" 
                                       placeholder="user@university.edu" value="{{ old('email') }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </section>

                    <section class="mb-5">
                        <h6 class="text-uppercase text-primary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">Role & Organization</h6>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">System Role</label>
                            <select name="role_id" class="form-select border-0 bg-light @error('role_id') is-invalid @enderror" required>
                                <option value="" selected disabled>Select a role...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}" {{ old('role_id') == $role->role_id ? 'selected' : '' }}>
                                        {{ $role->role_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="p-4 rounded-4 bg-light mb-4">
                            <p class="small text-muted mb-3 fw-medium">Primary Affiliation (Choose One)</p>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label x-small text-muted text-uppercase">Faculty</label>
                                    <select name="faculty_id" class="form-select border-white">
                                        <option value="">None</option>
                                        @foreach($faculties as $faculty)
                                            <option value="{{ $faculty->faculty_id }}">{{ $faculty->faculty_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label x-small text-muted text-uppercase">Institute</label>
                                    <select name="institute_id" class="form-select border-white">
                                        <option value="">None</option>
                                        @foreach($institutes as $institute)
                                            <option value="{{ $institute->institute_id }}">{{ $institute->institute_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label x-small text-muted text-uppercase">Office</label>
                                    <select name="office_id" class="form-select border-white">
                                        <option value="">None</option>
                                        @foreach($offices as $office)
                                            <option value="{{ $office->office_id }}">{{ $office->office_code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Department (Optional)</label>
                                <select name="dept_id" class="form-select border-0 bg-light">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->dept_id }}">{{ $department->dept_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Unit (Optional)</label>
                                <select name="unit_id" class="form-select border-0 bg-light">
                                    <option value="">Select Unit</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->unit_id }}">{{ $unit->unit_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h6 class="text-uppercase text-primary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">Contact & Security</h6>
                        <div class="row mb-4 g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Phone Number</label>
                                <input type="text" name="phone" class="form-control border-0 bg-light" placeholder="+234 XXX..." value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Account Status</label>
                                <select name="status" class="form-select border-0 bg-light">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-0">
                            <label class="form-label fw-semibold text-dark small">Residential / Office Address</label>
                            <textarea name="address" class="form-control border-0 bg-light" rows="2">{{ old('address') }}</textarea>
                        </div>
                    </section>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="p-4 rounded-4 mb-4 shadow-sm" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); color: #fff;">
                <h6 class="fw-bold mb-3 small text-uppercase" style="letter-spacing: 0.05em;">Security Notice</h6>
                <p class="small opacity-75 mb-0">A default password will be generated: <br>
                <span class="badge bg-white bg-opacity-10 mt-2 p-2 fw-mono">username@ {{ now()->year }}</span></p>
                <hr class="my-3 opacity-25">
                <p class="small opacity-75 mb-0">The user will be prompted to update their password immediately upon their first login.</p>
            </div>

            <div class="p-4 rounded-4 bg-white shadow-sm border-0">
                <h6 class="fw-bold text-dark mb-3 small text-uppercase">Quick Tips</h6>
                <ul class="list-unstyled mb-0">
                    <li class="d-flex mb-3">
                        <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                        <span class="small text-muted">Use the institutional email address for better tracking.</span>
                    </li>
                    <li class="d-flex">
                        <i class="fas fa-check-circle text-success me-2 mt-1"></i>
                        <span class="small text-muted">Assigning a Faculty/Institute automatically restricts the user's scope.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control, .form-select {
        padding: 0.75rem 1rem;
        border-radius: 10px;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        border: 1px solid #0d6efd !important;
    }
    .x-small { font-size: 0.65rem; font-weight: 800; }
    .btn-white { background-color: #fff; }
</style>
@endsection