@extends('layouts.admin')

@section('title', 'Add New Office | College of Medicine')

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
                    <h4 class="fw-semibold mb-0 text-gray-800">Add New Office</h4>
                    <p class="text-muted mb-0">Create a new administrative unit for the college</p>
                </div>
            </div>
        </div>
        
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('admin.offices.index') }}" class="btn btn-outline-secondary px-4 fw-medium">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
            <button type="submit" form="officeForm" class="btn btn-warning text-white bg-gradient-to-r from-amber-500 to-amber-600 px-4 fw-bold border-0 shadow-sm">
                <i class="fas fa-save me-1"></i> Save Office
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            {{-- Main Form Card --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-building me-2 text-amber-600"></i> Office Details
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.offices.store') }}" method="POST" id="officeForm">
                        @csrf

                        {{-- General Information --}}
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3" style="width:35px; height:35px; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-info fa-sm"></i>
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
                                       placeholder="e.g. Office of the Provost" 
                                       value="{{ old('office_name') }}" 
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
                                           class="form-control text-uppercase @error('office_code') is-invalid @enderror" 
                                           placeholder="e.g. PROV" 
                                           value="{{ old('office_code') }}">
                                    @error('office_code') 
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
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3" style="width:35px; height:35px; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-user-tie fa-sm"></i>
                                </div>
                                <h6 class="fw-semibold text-gray-700 mb-0">Leadership & Location</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium text-gray-700 mb-2">Head of Office</label>
                                <select id="office_head_id" name="office_head_id" class="form-select select2-remote @error('office_head_id') is-invalid @enderror">
                                    <option value="">Search and select staff member...</option>
                                </select>
                                @error('office_head_id') 
                                    <div class="invalid-feedback">{{ $message }}</div> 
                                @enderror
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-medium text-gray-700 mb-2">Office Location</label>
                                <textarea name="location" 
                                          class="form-control" 
                                          rows="3" 
                                          placeholder="e.g. Ground Floor, Administrative Block, Main Campus">{{ old('location') }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert alert-light border d-flex align-items-center p-3 shadow-sm">
                <i class="fas fa-info-circle text-primary me-3"></i>
                <div class="small">
                    <strong>Note:</strong> Offices are top-level administrative containers. You can add specific <strong>Units</strong> to this office after saving.
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
                        <h6 class="fw-semibold text-gray-700 mb-3">Office Setup</h6>
                        <div class="list-group list-group-flush">
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex align-items-start">
                                    <div class="bg-green-50 text-green-600 rounded-circle p-1 me-3 mt-1">
                                        <i class="fas fa-check fa-xs"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium d-block text-gray-800">Inventory Mapping</span>
                                        <span class="text-muted small">Items can be assigned directly to an office or its sub-units.</span>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item px-0 py-2 border-0">
                                <div class="d-flex align-items-start">
                                    <div class="bg-green-50 text-green-600 rounded-circle p-1 me-3 mt-1">
                                        <i class="fas fa-check fa-xs"></i>
                                    </div>
                                    <div>
                                        <span class="fw-medium d-block text-gray-800">Head of Office</span>
                                        <span class="text-muted small">This person will have oversight of all assets under this office.</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded p-3 border">
                        <h6 class="fw-semibold text-gray-700 mb-2">Example Data</h6>
                        <div class="small text-muted">
                            <div class="d-flex mb-1">
                                <span class="fw-medium me-2" style="min-width: 60px;">Name:</span>
                                <span>Bursary Department</span>
                            </div>
                            <div class="d-flex mb-1">
                                <span class="fw-medium me-2" style="min-width: 60px;">Code:</span>
                                <span class="text-amber-600 fw-medium">BURS</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats Card --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-chart-bar me-2 text-blue-600"></i> Current Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-blue-50 text-blue-600 rounded-circle p-2 me-3" style="width:35px; height:35px; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-building fa-sm"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold text-dark mb-0">{{ \App\Models\Office::count() }}</h5>
                            <p class="text-muted mb-0 small">Total Offices</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="bg-green-50 text-green-600 rounded-circle p-2 me-3" style="width:35px; height:35px; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-users fa-sm"></i>
                        </div>
                        <div>
                            {{-- Count only active staff that belong to an Office --}}
                            <h5 class="fw-bold text-dark mb-0">
                                {{ $activeOfficeStaff ?? 'N/A' }}
                            </h5>
                            <p class="text-muted mb-0 small">ActiveStaff in Offices</p>
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
    .select2-container--bootstrap-5 .select2-selection { border-color: #dee2e6; height: calc(3.5rem + 2px); }
    .bg-gray-50 { background-color: #f8fafc; }
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