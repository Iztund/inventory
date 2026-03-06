@extends('layouts.admin')

@section('title', 'Add New Faculty')

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
                    <h4 class="fw-semibold mb-0 text-gray-800">Add New Faculty</h4>
                    <p class="text-muted mb-0">Create a new academic faculty</p>
                </div>
            </div>
        </div>
        
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('admin.faculties.index') }}" class="btn btn-outline-secondary px-4">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
            <button type="submit" form="facultyForm" class="btn btn-warning text-white bg-gradient-to-r from-amber-500 to-amber-600 px-4">
                <i class="fas fa-save me-1"></i> Save Faculty
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
                    <form action="{{ route('admin.faculties.store') }}" method="POST" id="facultyForm">
                        @csrf

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
                                       placeholder="e.g. Faculty of Clinical Sciences" 
                                       value="{{ old('faculty_name') }}" 
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
                                           placeholder="e.g. FCS" 
                                           value="{{ old('faculty_code') }}">
                                    @error('faculty_code') 
                                        <div class="invalid-feedback">{{ $message }}</div> 
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-gray-700 mb-2">Status</label>
                                    <select name="is_active" class="form-select">
                                        <option value="active" {{ old('is_active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('is_active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="hidden" {{ old('is_active') == 'hidden' ? 'selected' : '' }}>Hidden</option>
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
                            
                           <select id="faculty_dean_id" name="faculty_dean_id" class="form-select select2-remote">
                                <option value="">Select Dean</option>
                                {{-- No need for @foreach here if using AJAX --}}
                            </select>
                            <div>
                                <label class="form-label fw-medium text-gray-700 mb-2">Faculty Address</label>
                                <textarea name="faculty_address" 
                                          class="form-control" 
                                          rows="3" 
                                          placeholder="Building, Block, Office of the Dean...">{{ old('faculty_address') }}</textarea>
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
                        <h6 class="fw-semibold text-gray-700 mb-3">Best Practices</h6>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex align-items-start">
                                    <div class="bg-green-50 text-green-600 rounded-circle p-1 me-3 mt-1">
                                        <i class="fas fa-check fa-xs"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium d-block text-gray-800">Unique Codes</span>
                                        <span class="text-muted small">Use short, memorable codes (e.g., FCS, MED, LAW)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex align-items-start">
                                    <div class="bg-green-50 text-green-600 rounded-circle p-1 me-3 mt-1">
                                        <i class="fas fa-check fa-xs"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium d-block text-gray-800">Dean Assignment</span>
                                        <span class="text-muted small">Assign an active user as dean for proper management</span>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex align-items-start">
                                    <div class="bg-green-50 text-green-600 rounded-circle p-1 me-3 mt-1">
                                        <i class="fas fa-check fa-xs"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium d-block text-gray-800">Clear Address</span>
                                        <span class="text-muted small">Include building and office location for easy reference</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded p-3 border">
                        <h6 class="fw-semibold text-gray-700 mb-2">Example Format</h6>
                        <div class="small text-muted">
                            <div class="d-flex mb-1">
                                <span class="fw-medium me-2" style="min-width: 60px;">Name:</span>
                                <span>Faculty of Clinical Sciences</span>
                            </div>
                            <div class="d-flex mb-1">
                                <span class="fw-medium me-2" style="min-width: 60px;">Code:</span>
                                <span class="text-amber-600 fw-medium">FCS</span>
                            </div>
                            <div class="d-flex">
                                <span class="fw-medium me-2" style="min-width: 60px;">Status:</span>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-chart-bar me-2 text-blue-600"></i> System Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-blue-50 text-blue-600 rounded-circle p-2 me-3">
                            <i class="fas fa-university"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">{{ \App\Models\Faculty::count() }}</h5>
                            <p class="text-muted mb-0 small">Total Faculties</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-green-50 text-green-600 rounded-circle p-2 me-3">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">{{ $activeFacultyUsers->count() }}</h5>
                            <p class="text-muted mb-0 small">Available Active Faculty Users</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @include('admin.partials.entity_scripts', [
        'formId' => 'facultyForm', 
        'searchRoute' => route('admin.search.staff')
    ])
@endpush
@endsection