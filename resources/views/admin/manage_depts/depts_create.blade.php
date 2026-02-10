@extends('layouts.admin')

@section('title', 'Add New Department | Inventory Management')

@section('content')
<div class="container-fluid px-4">
    {{-- Card Header: Consistent with Faculty/Office --}}
    <div class="card border-0 shadow-sm mb-4 mt-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                {{-- Left Side: Navigation --}}
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <a href="{{ route('admin.departments.index') }}" 
                       class="btn btn-light btn-sm border rounded-circle me-3 d-flex align-items-center justify-content-center shadow-sm" 
                       style="width: 40px; height: 40px;">
                        <i class="fas fa-chevron-left text-muted"></i>
                    </a>
                    <div>
                        <h4 class="fw-bold mb-0 text-gray-800 tracking-tight">Add New Department</h4>
                        <p class="text-muted mb-0 small mt-1">
                            Establish a new academic unit within the College of Medicine hierarchy
                        </p>
                    </div>
                </div>
                
                {{-- Right Side: Actions --}}
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary px-3 py-2 fw-medium border-0 text-muted">
                        Discard
                    </a>
                    <button type="submit" form="deptForm" class="btn btn-warning text-white bg-gradient-to-r from-amber-500 to-amber-700 px-3 py-2 border-0 shadow-sm fw-semibold d-inline-flex align-items-center flex-nowrap">
                        <i class="fas fa-plus me-2"></i> 
                        <span style="white-space: nowrap;">Create Department</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('admin.departments.store') }}" method="POST" id="deptForm">
                        @csrf

                        {{-- Section 1: Hierarchy --}}
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-sitemap small"></i>
                                </div>
                                <h6 class="fw-bold text-gray-700 mb-0 text-uppercase small" style="letter-spacing: 0.05rem;">Institutional Hierarchy</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium text-gray-700 mb-2">Parent Faculty <span class="text-danger">*</span></label>
                                {{-- ADDED id="faculty_id" BELOW --}}
                                <select id="faculty_id" name="faculty_id" class="form-select @error('faculty_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select the hosting faculty...</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->faculty_id }}" {{ old('faculty_id') == $faculty->faculty_id ? 'selected' : '' }}>
                                            {{ $faculty->faculty_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('faculty_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Section 2: Info --}}
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-info small"></i>
                                </div>
                                <h6 class="fw-bold text-gray-700 mb-0 text-uppercase small" style="letter-spacing: 0.05rem;">Department Details</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium text-gray-700 mb-2">Full Department Name <span class="text-danger">*</span></label>
                                <input type="text" name="dept_name" class="form-control form-control-lg @error('dept_name') is-invalid @enderror" placeholder="e.g., Department of Surgery" value="{{ old('dept_name') }}" required>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-gray-700 mb-2">Department Code</label>
                                    <input type="text" name="dept_code" class="form-control" placeholder="e.g., SURG" value="{{ old('dept_code') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-gray-700 mb-2">Initial Status</label>
                                    <select name="is_active" class="form-select">
                                        <option value="active" selected>Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Leadership --}}
                        <div>
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-tie small"></i>
                                </div>
                                <h6 class="fw-bold text-gray-700 mb-0 text-uppercase small" style="letter-spacing: 0.05rem;">Leadership & Location</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium text-gray-700 mb-2">Head of Department (HOD)</label>
                                <select id="dept_head_id" 
                                        name="dept_head_id" 
                                        class="form-select select2-remote"
                                        data-parent-filter="#faculty_id" 
                                        data-parent-type="faculty">
                                    <option value="">Search for staff...</option>
                                </select>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-medium text-gray-700 mb-2">Physical Location</label>
                                <textarea name="dept_address" class="form-control" rows="3" placeholder="e.g., West Wing, 2nd Floor">{{ old('dept_address') }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar Guide: Restored Administration Details --}}
        <div class="col-lg-4">
            {{-- Quick Tips --}}
            <div class="p-4 rounded-4 mb-4" style="background: #ecece6; border: 1px solid #f3a310;">
                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-lightbulb me-2"></i>Quick Tips</h6>
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-3 d-flex align-items-start">
                        <i class="fas fa-check-circle text-primary me-2 mt-1"></i>
                        <span>The <strong>Department Code</strong> is essential for medical asset tagging and filtering reports.</span>
                    </li>
                    <li class="d-flex align-items-start">
                        <i class="fas fa-info-circle text-primary me-2 mt-1"></i>
                        <span>Assigning an HOD allows them to approve inventory transfers for this department.</span>
                    </li>
                </ul>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark small text-uppercase">
                        <i class="fas fa-shield-alt me-2 text-amber-600"></i> Administration Guide
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h6 class="fw-bold text-gray-800 small mb-2">Naming Convention</h6>
                        <p class="text-muted small">
                            Always use the formal title as recognized by the University Senate.
                            <br><br>
                            <span class="text-dark fw-medium">Correct:</span> "Department of Paediatrics"<br>
                            <span class="text-danger fw-medium">Incorrect:</span> "Paed Dept"
                        </p>
                    </div>

                    <div class="p-3 rounded-3 bg-light border-start border-amber-500 border-4">
                        <h6 class="fw-bold text-dark small mb-1">Asset prefixing</h6>
                        <p class="small text-muted mb-0">
                            The Department Code will prefixed to all medical equipment QR codes for this unit.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Checklist --}}
            <div class="card border-0 shadow-sm bg-dark text-white rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold small text-uppercase mb-3" style="color: #fbbf24;">Submission Checklist</h6>
                    <div class="d-flex align-items-center mb-2 small text-white-50">
                        <i class="fas fa-check-circle me-2 text-success"></i> Unique Name Verified
                    </div>
                    <div class="d-flex align-items-center mb-2 small text-white-50">
                        <i class="fas fa-check-circle me-2 text-success"></i> Parent Faculty Selected
                    </div>
                    <div class="d-flex align-items-center small text-white-50">
                        <i class="fas fa-circle me-2" style="font-size: 0.5rem;"></i> Staff (HOD) Optional
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .bg-gradient-to-r { background-image: linear-gradient(to right, var(--tw-gradient-stops)); }
    .from-amber-500 { --tw-gradient-from: #f59e0b; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(245, 158, 11, 0)); }
    .to-amber-600 { --tw-gradient-to: #d97706; }
    .select2-container--default .select2-selection--single { height: 48px; border: 1px solid #d1d5db; border-radius: 8px; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 46px; padding-left: 1rem; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@include('admin.partials.entity_scripts', [
    'formId' => 'deptForm', 
    'searchRoute' => route('admin.search.staff')
])
@endpush
@endsection