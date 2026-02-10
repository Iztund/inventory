@extends('layouts.admin')

@section('title', 'Edit Unit: ' . $unit->unit_name)

@section('content')
<div class="container-fluid px-4">
    {{-- Page Header --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                {{-- Left Side: Identity & Navigation --}}
                <div class="d-flex align-items-center mb-3 mb-md-0">
                    <a href="{{ route('admin.units.index') }}" 
                    class="btn btn-light btn-sm border rounded-circle me-4 d-flex align-items-center justify-content-center shadow-sm" 
                    style="width: 42px; height: 42px; transition: all 0.2s;">
                        <i class="fas fa-arrow-left text-muted"></i>
                    </a>
                    <div>
                        <h4 class="fw-bold mb-1 text-gray-800 tracking-tight">
                            Edit Unit: <span class="text-amber-600">{{ $unit->unit_name }}</span>
                        </h4>
                        <p class="text-muted mb-0 small mt-1">
                            Update functional unit details and administrative office alignment
                        </p>
                    </div>
                </div>
                
                {{-- Right Side: Actions --}}
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('admin.units.index') }}" 
                    class="btn btn-outline-secondary px-3 py-2 fw-medium border-0 shadow-none text-muted" 
                    style="font-size: 0.9rem;">
                        Cancel
                    </a>
                    
                    <button type="submit" form="unitForm" 
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
                        <i class="fas fa-edit me-2 text-amber-600"></i> Unit Details
                    </h5>
                </div>
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.units.update', $unit->unit_id) }}" method="POST" id="unitForm">
                        @csrf
                        @method('PUT')

                        {{-- Hierarchy (Offices > Units) --}}
                        <div class="mb-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="bg-amber-50 text-amber-600 rounded-circle p-2 me-3" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-sitemap small"></i>
                                </div>
                                <h6 class="fw-semibold text-gray-700 mb-0">Administrative Hierarchy</h6>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label fw-medium text-gray-700 mb-2">Parent Office <span class="text-danger">*</span></label>
                                <select id="office_id" name="office_id" class="form-select @error('office_id') is-invalid @enderror" required>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->office_id }}" 
                                            {{ old('office_id', $unit->office_id) == $office->office_id ? 'selected' : '' }}>
                                            {{ $office->office_name }} ({{ $office->office_code }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text small opacity-75">Select the Office this unit reports to.</div>
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
                                <label class="form-label fw-medium text-gray-700 mb-2">Unit Name <span class="text-danger">*</span></label>
                                <input type="text" name="unit_name" class="form-control form-control-lg @error('unit_name') is-invalid @enderror" 
                                       value="{{ old('unit_name', $unit->unit_name) }}" required>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-gray-700 mb-2">Unit Code</label>
                                    <input type="text" name="unit_code" class="form-control @error('unit_code') is-invalid @enderror" 
                                           value="{{ old('unit_code', $unit->unit_code) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-gray-700 mb-2">Operation Status</label>
                                    <select name="is_active" class="form-select">
                                        <option value="active" {{ $unit->is_active == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ $unit->is_active == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <label class="form-label fw-medium text-gray-700 mb-2">Unit Head / Coordinator</label>
                                <select id="unit_head_id" 
                                        name="unit_head_id" 
                                        class="form-select select2-remote @error('unit_head_id') is-invalid @enderror"
                                        data-parent-filter="#office_id" 
                                        data-parent-type="office">
                                    @if($unit->head)
                                        <option value="{{ $unit->unit_head_id }}" selected>
                                            {{ $unit->head->full_name }} ({{ $unit->head->email }})
                                        </option>
                                    @else
                                        <option value="">Search for staff...</option>
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label class="form-label fw-medium text-gray-700 mb-2">Physical Location</label>
                                <textarea name="unit_location" class="form-control" rows="3" placeholder="e.g. Clinical Science Building, 3rd Floor">{{ old('unit_location', $unit->unit_location) }}</textarea>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- 1. Entity Context Card --}}
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-project-diagram me-2 text-amber-600"></i> Unit Context
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="p-4">
                        {{-- Hierarchy Visualization --}}
                        <div class="d-flex align-items-center mb-4">
                            <div class="position-relative d-flex flex-column align-items-center">
                                <div class="rounded-circle bg-slate-100 d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px;">
                                    <i class="fas fa-building text-slate-500 small"></i>
                                </div>
                                <div class="bg-slate-300" style="width: 2px; height: 20px;"></div>
                                <div class="rounded-circle bg-amber-100 d-flex align-items-center justify-content-center border border-amber-200" style="width: 40px; height: 40px;">
                                    <i class="fas fa-layer-group text-amber-600 small"></i>
                                </div>
                            </div>
                            <div class="ms-3">
                                <div class="mb-3">
                                    <small class="text-muted d-block uppercase tracking-wider" style="font-size: 0.65rem;">Parent Office</small>
                                    <span class="fw-bold text-slate-700">{{ $unit->office->office_name ?? 'Unassigned' }}</span>
                                </div>
                                <div>
                                    <small class="text-muted d-block uppercase tracking-wider" style="font-size: 0.65rem;">Current Unit</small>
                                    <span class="fw-bold text-amber-600">{{ $unit->unit_name }}</span>
                                </div>
                            </div>
                        </div>

                        <p class="small text-muted mb-0">
                            A <strong>Unit</strong> is a specialized functional arm of an <strong>Office</strong>. In the College of Medicine, inventory assigned to this unit is ultimately overseen by the Office Head.
                        </p>
                    </div>
                </div>
            </div>

            {{-- 2. Inventory Footprint --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">
                        <i class="fas fa-boxes me-2 text-amber-600"></i> Inventory Summary
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 rounded-3 bg-light text-center border">
                                <h4 class="fw-bold text-dark mb-1">{{ $unit->assets_count ?? 0 }}</h4>
                                <small class="text-muted uppercase" style="font-size: 0.6rem;">Total Assets</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 bg-light text-center border">
                                <h4 class="fw-bold text-success mb-1">{{ $unit->active_assets_count ?? 0 }}</h4>
                                <small class="text-muted uppercase" style="font-size: 0.6rem;">In Use</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="small text-muted">Last Audit Date</span>
                            <span class="small fw-medium">{{ $unit->last_audit_date ?? 'Never' }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="small text-muted">Custodian</span>
                            <span class="small fw-medium text-truncate ms-2" style="max-width: 120px;">
                                {{ $unit->head->full_name ?? 'None' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. Guidance Note --}}
            <div class="bg-amber-50 rounded-4 p-4 border border-amber-100 shadow-sm">
                <div class="d-flex align-items-start">
                    <div class="bg-amber-500 text-white rounded-circle p-2 me-3 flex-shrink-0" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-shield-alt small"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-amber-900 mb-1" style="font-size: 0.85rem;">Administrative Note</h6>
                        <p class="small text-amber-800 mb-0 opacity-75">
                            Changing the parent office will move all associated assets to the new office's jurisdiction in the next valuation report.
                        </p>
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
    'formId' => 'unitForm', 
    'searchRoute' => route('admin.search.staff')
])
@endpush
@endsection