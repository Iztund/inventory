@extends('layouts.admin')

@section('title', 'Add New Unit')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 pt-4">
        <div>
            <div class="d-flex align-items-center mb-2">
                <a href="{{ route('admin.units.index') }}" 
                   class="btn btn-light btn-sm border rounded-circle me-3 d-flex align-items-center justify-content-center" 
                   style="width: 36px; height: 36px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-semibold mb-0 text-gray-800">Create Functional Unit</h4>
                    <p class="text-muted mb-0">Define a new operational unit within the inventory system</p>
                </div>
            </div>
        </div>
        
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('admin.units.index') }}" class="btn btn-outline-secondary px-4">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
            <button type="submit" form="unitForm" class="btn btn-warning text-white bg-gradient-to-r from-amber-500 to-amber-600 px-4 shadow-sm">
                <i class="fas fa-save me-1"></i> Save Unit
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            {{-- Main Form Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-edit me-2 text-amber-600"></i> Unit Details
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.units.store') }}" method="POST" id="unitForm">
                        @csrf

                        {{-- Section 1: Hierarchy --}}
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3">
                                    <i class="fas fa-link"></i>
                                </div>
                                <h6 class="fw-semibold text-gray-700 mb-0">Organizational Placement</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium text-gray-700 mb-2">
                                    Parent Office / Department <span class="text-danger">*</span>
                                </label>
                                <select id="office_id" name="office_id" class="form-select form-select-lg @error('office_id') is-invalid @enderror" required>
                                    <option value="">-- Select Parent Office --</option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->office_id }}" {{ old('office_id') == $office->office_id ? 'selected' : '' }}>
                                            {{ $office->office_name }} ({{ $office->office_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('office_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Section 2: Identity --}}
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <h6 class="fw-semibold text-gray-700 mb-0">Unit Identity</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium text-gray-700 mb-2">Unit Name <span class="text-danger">*</span></label>
                                <input type="text" name="unit_name" class="form-control form-control-lg @error('unit_name') is-invalid @enderror" 
                                       placeholder="e.g. Network Operations Center" value="{{ old('unit_name') }}" required>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-gray-700 mb-2">Unit Code</label>
                                    <input type="text" name="unit_code" class="form-control @error('unit_code') is-invalid @enderror" 
                                           placeholder="e.g. NOC-01" value="{{ old('unit_code') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-gray-700 mb-2">Status</label>
                                    <select name="is_active" class="form-select">
                                        <option value="active" {{ old('is_active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('is_active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Leadership --}}
                        <div>
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <h6 class="fw-semibold text-gray-700 mb-0">Leadership & Location</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium text-gray-700 mb-2">Unit Head / Coordinator</label>
                                <select id="unit_head_id" 
                                        name="unit_head_id" 
                                        class="form-select select2-remote"
                                        data-parent-filter="#office_id" 
                                        data-parent-type="office">
                                    <option value="">Search for staff...</option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label fw-medium text-gray-700 mb-2">Physical Location</label>
                                <textarea name="unit_location" class="form-control" rows="3" 
                                          placeholder="Building, Floor, Room Number...">{{ old('unit_location') }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-light border d-flex align-items-center p-3">
                <i class="fas fa-info-circle text-primary me-3"></i>
                <div class="small">
                    <strong>Note:</strong> Unit name is required and should be descriptive of its specific function.
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
                                        <span class="text-muted small">Use short, functional codes (e.g., NOC, LAB-01)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex align-items-start">
                                    <div class="bg-green-50 text-green-600 rounded-circle p-1 me-3 mt-1">
                                        <i class="fas fa-check fa-xs"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium d-block text-gray-800">Parent Office</span>
                                        <span class="text-muted small">Ensure the unit is mapped to the correct Department</span>
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
                                <span>Network Operations Center</span>
                            </div>
                            <div class="d-flex mb-1">
                                <span class="fw-medium me-2" style="min-width: 60px;">Code:</span>
                                <span class="text-amber-600 fw-medium">NOC-01</span>
                            </div>
                            <div class="d-flex">
                                <span class="fw-medium me-2" style="min-width: 60px;">Status:</span>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- System Overview Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-chart-bar me-2 text-blue-600"></i> System Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-blue-50 text-blue-600 rounded-circle p-2 me-3">
                            <i class="fas fa-layer-group"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">{{ \App\Models\Unit::count() }}</h5>
                            <p class="text-muted mb-0 small">Total Units</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-green-50 text-green-600 rounded-circle p-2 me-3">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">{{ $activeUnitUsers->count() }}</h5>
                            <p class="text-muted mb-0 small">Available Active Unit Users</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@include('admin.partials.entity_scripts', [
    'formId' => 'unitForm', 
    'searchRoute' => route('admin.search.staff')
])
@endpush