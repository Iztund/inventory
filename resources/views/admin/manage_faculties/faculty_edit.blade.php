@extends('layouts.admin')

@section('title', 'Edit Faculty')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 pt-4">
        <div>
            <div class="d-flex align-items-center mb-2">
                <a href="{{ route('admin.faculties.index') }}" 
                   class="btn btn-light btn-sm border rounded-circle me-3 d-flex align-items-center justify-content-center" 
                   style="width: 36px; height: 36px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-semibold mb-0 text-gray-800">Edit Faculty: {{ $faculty->faculty_name }}</h4>
                    <p class="text-muted mb-0">Update faculty details and information</p>
                </div>
            </div>
        </div>
        
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary px-4">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
            <button type="submit" form="facultyForm" class="btn btn-warning text-white bg-gradient-to-r from-amber-500 to-amber-600 px-4">
                <i class="fas fa-save me-1"></i> Update Faculty
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            {{-- Main Form Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-edit me-2 text-amber-600"></i> Faculty Details
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.faculties.update', $faculty->faculty_id) }}" method="POST" id="facultyForm">
                        @csrf
                        @method('PUT')

                        {{-- General Information --}}
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3">
                                    <i class="fas fa-info"></i>
                                </div>
                                <h6 class="fw-semibold text-gray-700 mb-0">General Information</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium text-gray-700 mb-2">
                                    Faculty Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="faculty_name" 
                                       class="form-control form-control-lg @error('faculty_name') is-invalid @enderror" 
                                       value="{{ old('faculty_name', $faculty->faculty_name) }}" 
                                       required>
                                @error('faculty_name') 
                                    <div class="invalid-feedback">{{ $message }}</div> 
                                @enderror
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-gray-700 mb-2">Faculty Code</label>
                                    <input type="text" 
                                           name="faculty_code" 
                                           class="form-control @error('faculty_code') is-invalid @enderror" 
                                           value="{{ old('faculty_code', $faculty->faculty_code) }}">
                                    @error('faculty_code') 
                                        <div class="invalid-feedback">{{ $message }}</div> 
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-gray-700 mb-2">Status</label>
                                    <select name="is_active" class="form-select">
                                        <option value="active" {{ old('is_active', $faculty->is_active) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('is_active', $faculty->is_active) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="hidden" {{ old('is_active', $faculty->is_active) == 'hidden' ? 'selected' : '' }}>Hidden</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Leadership & Location --}}
                        <div>
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <h6 class="fw-semibold text-gray-700 mb-0">Leadership & Location</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium text-gray-700 mb-2">Dean of Faculty</label>
                                <select id="faculty_dean_id" 
                                        name="faculty_dean_id" 
                                        class="form-select select2 @error('faculty_dean_id') is-invalid @enderror"
                                        data-selected-dean="{{ old('faculty_dean_id', $faculty->faculty_dean_id) }}"
                                        data-selected-name="{{ $faculty->dean ? ($faculty->dean->full_name . ' (' . $faculty->dean->email . ')') : '' }}">
                                    @if($faculty->dean)
                                        <option value="{{ $faculty->faculty_dean_id }}" selected>
                                            {{ $faculty->dean->full_name }} ({{ $faculty->dean->email }})
                                        </option>
                                    @else
                                        <option value="">Select Dean</option>
                                    @endif
                                </select>
                                @error('faculty_dean_id') 
                                    <div class="invalid-feedback">{{ $message }}</div> 
                                @enderror
                                <div class="form-text text-muted small mt-1">
                                    Start typing to search for users. Minimum 2 characters required.
                                </div>
                            </div>

                            <div>
                                <label class="form-label fw-medium text-gray-700 mb-2">Faculty Address</label>
                                <textarea name="faculty_address" 
                                          class="form-control" 
                                          rows="3" 
                                          placeholder="Building, Block, Office of the Dean...">{{ old('faculty_address', $faculty->faculty_address) }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Form Note --}}
            <div class="alert alert-light border d-flex align-items-center p-3">
                <i class="fas fa-info-circle text-primary me-3"></i>
                <div class="small">
                    <strong>Note:</strong> Faculty name is required and must be unique within the system.
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Quick Guide Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-lightbulb me-2 text-amber-600"></i> Quick Guide
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="fw-semibold text-gray-700 mb-3">Update Guidelines</h6>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex align-items-start">
                                    <div class="bg-blue-50 text-blue-600 rounded-circle p-1 me-3 mt-1">
                                        <i class="fas fa-exclamation fa-xs"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium d-block text-gray-800">Name Changes</span>
                                        <span class="text-muted small">Faculty name changes require review</span>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex align-items-start">
                                    <div class="bg-blue-50 text-blue-600 rounded-circle p-1 me-3 mt-1">
                                        <i class="fas fa-exclamation fa-xs"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium d-block text-gray-800">Code Uniqueness</span>
                                        <span class="text-muted small">Faculty codes must remain unique</span>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex align-items-start">
                                    <div class="bg-green-50 text-green-600 rounded-circle p-1 me-3 mt-1">
                                        <i class="fas fa-check fa-xs"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium d-block text-gray-800">Dean Updates</span>
                                        <span class="text-muted small">You can change the dean at any time</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Faculty Info Card --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold text-dark">
                                <i class="fas fa-info-circle me-2 text-blue-600"></i> Current Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-gray-500 d-block mb-1">Faculty ID</small>
                                <code class="text-amber-600 fw-medium">{{ $faculty->faculty_id }}</code>
                            </div>
                            <div class="mb-3">
                                <small class="text-gray-500 d-block mb-1">Created</small>
                                <span class="fw-medium">{{ $faculty->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="mb-3">
                                <small class="text-gray-500 d-block mb-1">Last Updated</small>
                                <span class="fw-medium">{{ $faculty->updated_at->format('M d, Y') }}</span>
                            </div>
                            <div>
                                <small class="text-gray-500 d-block mb-1">Departments</small>
                                <span class="badge bg-gray-100 text-gray-800 fw-medium px-3 py-2">
                                    {{ $department_count ?? 0 }} departments
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Status Overview Card --}}
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold text-dark">
                                <i class="fas fa-chart-pie me-2 text-green-600"></i> Status Overview
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-gray-500">Current Status</small>
                                    @if($faculty->is_active == 'active')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3">
                                            Active
                                        </span>
                                    @elseif($faculty->is_active == 'inactive')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3">
                                            Inactive
                                        </span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill px-3">
                                            Hidden
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <small class="text-gray-500 d-block mb-1">Current Dean</small>
                                @if($faculty->dean)
                                    <div class="d-flex align-items-center mt-1">
                                        <div class="bg-amber-100 text-amber-800 rounded-circle d-flex align-items-center justify-content-center fw-medium me-2" style="width: 28px; height: 28px;">
                                            {{ strtoupper(substr($faculty->dean->username, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="d-block fw-medium">{{ $faculty->dean->full_name }}</span>
                                            <small class="text-muted">{{ $faculty->dean->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic">No dean assigned</span>
                                @endif
                            </div>
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
    .select2-container--default .select2-selection--single {
        height: 48px;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 46px;
        padding-left: 0.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @include('admin.partials.entity_scripts', [
        'formId' => 'facultyForm', 
        'searchRoute' => route('admin.search.staff')
    ])
@endpush
@endsection