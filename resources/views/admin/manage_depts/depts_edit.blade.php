@extends('layouts.admin')

@section('title', 'Edit Department')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header (Cloned from Office design) --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                {{-- Left Side: Identity & Navigation --}}
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <a href="{{ route('admin.departments.index') }}" 
                    class="btn btn-light btn-sm border rounded-circle me-4 d-flex align-items-center justify-content-center shadow-sm" 
                    style="width: 42px; height: 42px; transition: all 0.2s;">
                        <i class="fas fa-arrow-left text-muted"></i>
                    </a>
                    <div>
                        <h4 class="fw-bold mb-1 text-gray-800 tracking-tight">
                            Edit Department: <span class="text-amber-600">{{ $department->dept_name }}</span>
                        </h4>
                        {{-- Added margin-top to separate description from title --}}
                        <p class="text-muted mb-0 small mt-1">
                            Update academic department details and faculty alignment
                        </p>
                    </div>
                </div>
                
                {{-- Right Side: Actions --}}
                {{-- Increased gap between buttons for a cleaner look --}}
                <div class="d-flex align-items-center gap-2">
                    {{-- Refined Cancel Button (Outline Style) --}}
                    <a href="{{ route('admin.departments.index') }}" 
                    class="btn btn-outline-secondary px-3 py-2 fw-medium border-0 shadow-none text-muted" 
                    style="font-size: 0.9rem;">
                        Cancel
                    </a>
                    
                    {{-- Compact Update Button --}}
                    <button type="submit" form="deptForm" 
                            class="btn btn-warning text-white bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-2 border-0 shadow-sm fw-semibold d-inline-flex align-items-center" 
                            style="width: auto; min-width: 160px; justify-content: center;">
                        <i class="fas fa-save me-2"></i> Update
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            {{-- Main Form Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-edit me-2 text-amber-600"></i> Department Details
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.departments.update', $department->dept_id) }}" method="POST" id="deptForm">
                        @csrf
                        @method('PUT')

                        {{-- Hierarchy (Specific to Departments) --}}
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-sitemap small"></i>
                                </div>
                                <h6 class="fw-semibold text-gray-700 mb-0">Institutional Hierarchy</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium text-gray-700 mb-2">Parent Faculty <span class="text-danger">*</span></label>
                                <select id="faculty_id" name="faculty_id" class="form-select @error('faculty_id') is-invalid @enderror" required>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->faculty_id }}" 
                                            {{ old('faculty_id', $department->faculty_id) == $faculty->faculty_id ? 'selected' : '' }}>
                                            {{ $faculty->faculty_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- General Information --}}
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-info small"></i>
                                </div>
                                <h6 class="fw-semibold text-gray-700 mb-0">General Information</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium text-gray-700 mb-2">Department Name <span class="text-danger">*</span></label>
                                <input type="text" name="dept_name" class="form-control form-control-lg @error('dept_name') is-invalid @enderror" 
                                       value="{{ old('dept_name', $department->dept_name) }}" required>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-gray-700 mb-2">Department Code</label>
                                    <input type="text" name="dept_code" class="form-control @error('dept_code') is-invalid @enderror" 
                                           value="{{ old('dept_code', $department->dept_code) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-gray-700 mb-2">Status</label>
                                    <select name="is_active" class="form-select">
                                        <option value="active" {{ $department->is_active == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $department->is_active == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Leadership & Location --}}
                        <div>
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-tie small"></i>
                                </div>
                                <h6 class="fw-semibold text-gray-700 mb-0">Leadership & Location</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium text-gray-700 mb-2">Head of Department (HOD)</label>
                                <select id="leader_id" 
                                        name="dept_head_id" 
                                        class="form-select select2-remote @error('dept_head_id') is-invalid @enderror"
                                        data-parent-filter="#faculty_id" 
                                        data-parent-type="faculty">
                                    @if($department->head)
                                        <option value="{{ $department->dept_head_id }}" selected>
                                            {{ $department->head->full_name }} ({{ $department->head->email }})
                                        </option>
                                    @else
                                        <option value="">Search for staff...</option>
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label class="form-label fw-medium text-gray-700 mb-2">Physical Location</label>
                                <textarea name="dept_address" class="form-control" rows="3">{{ old('dept_address', $department->dept_address) }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Form Note --}}
            <div class="alert alert-light border d-flex align-items-center p-3">
                <i class="fas fa-info-circle text-primary me-3"></i>
                <div class="small">
                    <strong>Note:</strong> Changes to the department name or parent faculty will affect all linked inventory items and historical records.
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Quick Guide Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-lightbulb me-2 text-amber-600"></i> Administration
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <h6 class="fw-semibold text-gray-700 mb-3 small text-uppercase">System Audit Note</h6>
                        <div class="p-3 rounded-3 bg-light border-start border-amber-500 border-4">
                            <p class="small text-muted mb-0">
                                Setting status to <strong>Inactive</strong> preserves audit logs but prevents new inventory entries for this department.
                            </p>
                        </div>
                    </div>

                    {{-- Metrics --}}
                    <div class="card border-0 shadow-sm mb-4 bg-light">
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-gray-500 d-block mb-1">Department ID</small>
                                <code class="text-amber-600 fw-medium">DEPT-{{ str_pad($department->dept_id, 4, '0', STR_PAD_LEFT) }}</code>
                            </div>
                            <div class="mb-0">
                                <small class="text-gray-500 d-block mb-1">Assigned Assets</small>
                                <span class="badge bg-blue-100 text-blue-800 fw-medium px-3 py-2">
                                    {{ $department->assets_count ?? 0 }} items
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Status Card --}}
                    <div class="card border-0 shadow-sm bg-white border">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <small class="text-gray-500">Visibility</small>
                                <span class="badge bg-{{ $department->is_active == 'active' ? 'success' : 'danger' }} bg-opacity-10 text-{{ $department->is_active == 'active' ? 'success' : 'danger' }} border rounded-pill px-3">
                                    {{ ucfirst($department->is_active) }}
                                </span>
                            </div>
                            <small class="text-gray-500 d-block mb-1">Created on</small>
                            <span class="small fw-medium">{{ $department->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Cloned Office styling */
    .bg-gradient-to-r { background-image: linear-gradient(to right, var(--tw-gradient-stops)); }
    .from-amber-500 { --tw-gradient-from: #f59e0b; --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgba(245, 158, 11, 0)); }
    .to-amber-600 { --tw-gradient-to: #d97706; }
    
    .select2-container--default .select2-selection--single {
        height: 48px; border: 1px solid #d1d5db; border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 46px; padding-left: 0.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
{{-- Your Partial Entity Script --}}
@include('admin.partials.entity_scripts', [
    'formId' => 'deptForm', 
    'searchRoute' => route('admin.search.staff')
])
@endpush
@endsection