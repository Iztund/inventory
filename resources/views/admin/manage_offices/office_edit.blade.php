@extends('layouts.admin')

@section('title', 'Edit Office')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 pt-4">
        <div>
            <div class="d-flex align-items-center mb-2">
                <a href="{{ route('admin.offices.index') }}" 
                   class="btn btn-light btn-sm border rounded-circle me-3 d-flex align-items-center justify-content-center" 
                   style="width: 36px; height: 36px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-semibold mb-0 text-gray-800">Edit Office: {{ $office->office_name }}</h4>
                    <p class="text-muted mb-0">Update administrative office details and location</p>
                </div>
            </div>
        </div>
        
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('admin.offices.index') }}" class="btn btn-outline-secondary px-4">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
            <button type="submit" form="officeForm" class="btn btn-warning text-white bg-gradient-to-r from-amber-500 to-amber-600 px-4">
                <i class="fas fa-save me-1"></i> Update Office
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            {{-- Main Form Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-edit me-2 text-amber-600"></i> Office Details
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.offices.update', $office->office_id) }}" method="POST" id="officeForm">
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
                                    Office Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       name="office_name" 
                                       class="form-control form-control-lg @error('office_name') is-invalid @enderror" 
                                       value="{{ old('office_name', $office->office_name) }}" 
                                       required>
                                @error('office_name') 
                                    <div class="invalid-feedback">{{ $message }}</div> 
                                @enderror
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-gray-700 mb-2">Office Code</label>
                                    <input type="text" 
                                           name="office_code" 
                                           class="form-control @error('office_code') is-invalid @enderror" 
                                           value="{{ old('office_code', $office->office_code) }}">
                                    @error('office_code') 
                                        <div class="invalid-feedback">{{ $message }}</div> 
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-gray-700 mb-2">Status</label>
                                    <select name="is_active" class="form-select">
                                        <option value="active" {{ old('is_active', $office->is_active) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('is_active', $office->is_active) == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <label class="form-label fw-medium text-gray-700 mb-2">Head of Office</label>
                                <select id="leader_id" 
                                        name="office_head_id" {{-- Changed from head_id to office_head_id --}}
                                        class="form-select select2-remote @error('office_head_id') is-invalid @enderror">
                                    @if($office->head)
                                        <option value="{{ $office->head->user_id }}" selected>
                                            {{ $office->head->full_name }} ({{ $office->head->email }})
                                        </option>
                                    @else
                                        <option value="">Select Head</option>
                                    @endif
                                </select>
                                @error('office_head_id') 
                                    <div class="invalid-feedback">{{ $message }}</div> 
                                @enderror
                            </div>

                            <div>
                                <label class="form-label fw-medium text-gray-700 mb-2">Office Location</label>
                                <textarea name="location" 
                                          class="form-control" 
                                          rows="3" 
                                          placeholder="Building name, Floor, Room Number...">{{ old('location', $office->location) }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Form Note --}}
            <div class="alert alert-light border d-flex align-items-center p-3">
                <i class="fas fa-info-circle text-primary me-3"></i>
                <div class="small">
                    <strong>Note:</strong> Offices are administrative units. Changing the office name will reflect across all inventory items assigned to this office.
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
                        <h6 class="fw-semibold text-gray-700 mb-3">Guidelines</h6>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex align-items-start">
                                    <div class="bg-blue-50 text-blue-600 rounded-circle p-1 me-3 mt-1">
                                        <i class="fas fa-check fa-xs"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium d-block text-gray-800">Unique Code</span>
                                        <span class="text-muted small">Keep office codes standardized (e.g., ADM-01)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex align-items-start">
                                    <div class="bg-blue-50 text-blue-600 rounded-circle p-1 me-3 mt-1">
                                        <i class="fas fa-user fa-xs"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium d-block text-gray-800">Head Accountability</span>
                                        <span class="text-muted small">The Head is responsible for assigned assets</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Office Info Card --}}
                    <div class="card border-0 shadow-sm mb-4 bg-light">
                        <div class="card-body">
                            <div class="mb-3">
                                <small class="text-gray-500 d-block mb-1">Office ID</small>
                                <code class="text-amber-600 fw-medium">OFF-{{ str_pad($office->office_id, 4, '0', STR_PAD_LEFT) }}</code>
                            </div>
                            <div class="mb-3">
                                <small class="text-gray-500 d-block mb-1">Created</small>
                                <span class="fw-medium">{{ $office->created_at->format('M d, Y') }}</span>
                            </div>
                            <div class="mb-0">
                                <small class="text-gray-500 d-block mb-1">Inventory Items</small>
                                <span class="badge bg-blue-100 text-blue-800 fw-medium px-3 py-2">
                                    {{ $items_count ?? 0 }} items assigned
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Status Overview Card --}}
                    <div class="card border-0 shadow-sm bg-white">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="mb-0 fw-semibold text-dark">
                                <i class="fas fa-chart-pie me-2 text-green-600"></i> Status Overview
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-gray-500">Current Status</small>
                                    @if($office->is_active == 'active')
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3">Active</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger rounded-pill px-3">Inactive</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <small class="text-gray-500 d-block mb-2">Current Head</small>
                                @if($office->head)
                                    <div class="d-flex align-items-center">
                                        <div class="bg-amber-100 text-amber-800 rounded-circle d-flex align-items-center justify-content-center fw-medium me-2" style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr($office->head->username, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="d-block fw-medium text-dark small">{{ $office->head->full_name }}</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $office->head->email }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic small">No head assigned</span>
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
{{-- Using Select2 CSS directly for consistency --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
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
@include('admin.partials.entity_scripts', [
    'formId' => 'officeForm', 
    'searchRoute' => route('admin.search.staff')
])
@endpush
@endsection