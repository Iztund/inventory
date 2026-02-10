@extends('layouts.admin')

@section('title', 'Edit Institute | College of Medicine')

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5 pt-4">
        <div>
            <div class="d-flex align-items-center mb-2">
                <a href="{{ route('admin.institutes.index') }}" 
                   class="btn btn-light btn-sm border rounded-circle me-3 d-flex align-items-center justify-content-center" 
                   style="width: 36px; height: 36px;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h4 class="fw-semibold mb-0 text-gray-800">Edit Institute: {{ $institute->institute_name }}</h4>
                    <p class="text-muted mb-0">Update research body details and primary custodian</p>
                </div>
            </div>
        </div>
        
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="{{ route('admin.institutes.index') }}" class="btn btn-outline-secondary px-4">
                <i class="fas fa-times me-1"></i> Cancel
            </a>
            <button type="submit" form="instituteForm" class="btn btn-warning text-white bg-gradient-to-r from-amber-500 to-amber-600 px-4 border-0 shadow-sm">
                <i class="fas fa-save me-1"></i> Update Institute
            </button>
        </div>
    </div>

    {{-- Top Section: Form and Audit Trail --}}
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-flask me-2 text-amber-600"></i> Institute Details
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.institutes.update', $institute->institute_id) }}" method="POST" id="instituteForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3">
                                    <i class="fas fa-info"></i>
                                </div>
                                <h6 class="fw-semibold text-gray-700 mb-0">General Information</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label for="institute_name" class="form-label fw-medium text-gray-700 mb-2">Institute Name <span class="text-danger">*</span></label>
                                <input type="text" id="institute_name" name="institute_name" class="form-control form-control-lg @error('institute_name') is-invalid @enderror" value="{{ old('institute_name', $institute->institute_name) }}" required>
                                @error('institute_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="institute_code" class="form-label fw-medium text-gray-700 mb-2">Institute Code</label>
                                    <input type="text" id="institute_code" name="institute_code" class="form-control text-uppercase @error('institute_code') is-invalid @enderror" value="{{ old('institute_code', $institute->institute_code) }}">
                                    @error('institute_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="is_active" class="form-label fw-medium text-gray-700 mb-2">Status</label>
                                    <select id="is_active" name="is_active" class="form-select">
                                        <option value="active" {{ old('is_active', $institute->is_active) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('is_active', $institute->is_active) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <h6 class="fw-semibold text-gray-700 mb-0">Leadership & Location</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label for="institute_director_id" class="form-label fw-medium text-gray-700 mb-2">Institute Director</label>
                                <select id="institute_director_id" name="institute_director_id" class="form-select select2-remote @error('institute_director_id') is-invalid @enderror">
                                    @if($institute->director)
                                        <option value="{{ $institute->director->user_id }}" selected>{{ $institute->director->full_name }} ({{ $institute->director->email }})</option>
                                    @else
                                        <option value="">Select Director</option>
                                    @endif
                                </select>
                            </div>

                            <div class="mb-2">
                                <label for="institute_address" class="form-label fw-medium text-gray-700 mb-2">Physical Address</label>
                                <textarea id="institute_address" name="institute_address" class="form-control" rows="3" placeholder="Building name, Lab Number, etc...">{{ old('institute_address', $institute->institute_address) }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Audit Trail Card --}}
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-slate-800" style="font-size: 0.95rem;">
                        <i class="fas fa-history me-2 text-orange-600"></i> Audit Trail
                    </h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item px-4 py-3">
                            <small class="text-slate-400 d-block uppercase tracking-wider fw-bold mb-1" style="font-size: 0.65rem;">System ID</small>
                            <code class="text-orange-600 fw-bold">INST-{{ str_pad($institute->institute_id, 5, '0', STR_PAD_LEFT) }}</code>
                        </li>
                        <li class="list-group-item px-4 py-3">
                            <small class="text-slate-400 d-block uppercase tracking-wider fw-bold mb-1" style="font-size: 0.65rem;">Created On</small>
                            <span class="text-slate-700 fw-medium">{{ $institute->created_at->format('M d, Y @ H:i') }}</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Regulatory Guide --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-slate-800" style="font-size: 0.95rem;">
                        <i class="fas fa-gavel me-2 text-orange-600"></i> Regulations
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex gap-3 mb-3">
                        <div class="text-orange-600 mt-1"><i class="fas fa-shield-alt"></i></div>
                        <p class="small text-slate-600 mb-0"><strong>Code Integrity:</strong> Updates affect future asset tagging prefixes.</p>
                    </div>
                    <div class="d-flex gap-3">
                        <div class="text-orange-600 mt-1"><i class="fas fa-user-check"></i></div>
                        <p class="small text-slate-600 mb-0"><strong>Accountability:</strong> Directors must verify all medical assets upon assignment.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Section: Full Width Resource Summary --}}
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);">
                <div class="card-body p-4 py-5 text-white position-relative">
                    <i class="fas fa-microscope position-absolute end-0 bottom-0 mb-n4 me-n2 opacity-10" style="font-size: 10rem;"></i>
                    
                    <div class="row align-items-center position-relative" style="z-index: 2;">
                        <div class="col-md-4 mb-4 mb-md-0">
                            <h5 class="fw-black text-uppercase mb-2" style="letter-spacing: 0.15em;">Resource Summary</h5>
                            <p class="text-white text-opacity-50 small mb-0">Live data overview for the College of Medicine inventory audit.</p>
                        </div>
                        <div class="col-md-8">
                            <div class="row g-4">
                                {{-- Total Assets Stat --}}
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center p-3 rounded-4 bg-white bg-opacity-10">
                                        <div class="bg-orange-500 bg-opacity-20 rounded-3 p-3 me-3 text-orange-400">
                                            <i class="fas fa-boxes fa-2x"></i>
                                        </div>
                                        <div>
                                            <h2 class="fw-black mb-0 text-opacity-60">{{ $ItemCount ?? 0 }}</h2>
                                            <span class="text-white text-opacity-60 uppercase fw-bold small" style="font-size: 0.7rem; letter-spacing: 1px;">Total Assets Assigned</span>
                                        </div>
                                    </div>
                                </div>
                                {{-- Active Staff Stat --}}
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center p-3 rounded-4 bg-white bg-opacity-10">
                                        <div class="bg-blue-500 bg-opacity-20 rounded-3 p-3 me-3 text-blue-400">
                                            <i class="fas fa-users fa-2x"></i>
                                        </div>
                                        <div>
                                            <h2 class="fw-black mb-0 text-opacity-60">{{ $activeStaffs->count() ?? 0 }}</h2>
                                            <span class="text-white text-opacity-60 uppercase fw-bold small" style="font-size: 0.7rem; letter-spacing: 1px;">Active Research Staff</span>
                                        </div>
                                    </div>
                                </div>
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
    .select2-container--default .select2-selection--single { height: 48px; border: 1px solid #d1d5db; border-radius: 0.375rem; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 46px; padding-left: 0.75rem; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 46px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@include('admin.partials.entity_scripts', [
    'formId' => 'instituteForm', 
    'selectId' => 'institute_director_id',
    'searchRoute' => route('admin.search.staff')
])
@endpush
@endsection