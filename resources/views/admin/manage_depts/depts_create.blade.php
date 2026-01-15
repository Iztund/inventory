@extends('layouts.admin')

@section('title', 'Add New Department | Inventory Management')

@section('content')
{{-- Added a custom style block to handle Select2 and padding nuances --}}
<style>
    .form-control, .form-select, .select2-selection {
        padding: 0.75rem 1rem;
        border-radius: 10px !important;
        transition: all 0.2s ease;
    }
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        border: 1px solid #0d6efd !important;
    }
    .btn-dark { background-color: #1e293b; border: none; }
    .btn-dark:hover { background-color: #0f172a; }

    /* Select2 Responsive Fixes */
    .select2-container { width: 100% !important; }
    .select2-selection { 
        background-color: #f8fafc !important; 
        border: none !important; 
        height: auto !important;
        min-height: 45px;
        display: flex !important;
        align-items: center;
    }
    .select2-selection__arrow { height: 45px !important; }
</style>

<div class="container-fluid py-4 py-lg-5 px-3 px-lg-5" style="background-color: #f8fafc; min-height: 100vh;">
    
    {{-- Header Section: Stacks on mobile, inline on desktop --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 mb-lg-5 gap-3">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.departments.index') }}" 
               class="btn btn-white shadow-sm rounded-3 me-3 border-0 d-flex align-items-center justify-content-center" 
               style="width: 40px; height: 40px; background: white;">
                <i class="fas fa-chevron-left text-muted" style="font-size: 0.9rem;"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">Add Department</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small text-muted bg-transparent p-0">
                        <li class="breadcrumb-item">Organization</li>
                        <li class="breadcrumb-item active">New Department</li>
                    </ol>
                </nav>
            </div>
        </div>
        
        {{-- Actions: Pushed to the right on desktop, full width or right-aligned on mobile --}}
        <div class="d-flex align-items-center justify-content-md-end gap-2">
            <button type="button" onclick="window.history.back();" class="btn btn-link text-decoration-none text-muted fw-medium px-3">Discard</button>
            <button type="submit" form="deptForm" class="btn btn-dark px-4 py-2 rounded-3 fw-medium shadow-sm">
                Save <span class="d-none d-sm-inline">Department</span>
            </button>
        </div>
    </div>

    <div class="row g-4">
        {{-- Main Form Column --}}
        <div class="col-lg-8">
            <div class="bg-white rounded-4 shadow-sm p-3 p-md-5 border-0">
                <form action="{{ route('admin.departments.store') }}" method="POST" id="deptForm">
                    @csrf

                    {{-- Section 1: Hierarchy --}}
                    <section class="mb-5">
                        <h6 class="text-uppercase text-primary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">Faculty Hierarchy</h6>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Parent Faculty <span class="text-danger">*</span></label>
                            <select name="faculty_id" class="form-select border-0 bg-light @error('faculty_id') is-invalid @enderror" required>
                                <option value="" disabled selected>-- Select the parent faculty --</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->faculty_id }}" {{ old('faculty_id') == $faculty->faculty_id ? 'selected' : '' }}>
                                        {{ $faculty->faculty_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text small text-muted">Links this department to the main College of Medicine faculty.</div>
                            @error('faculty_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </section>

                    {{-- Section 2: General Info --}}
                    <section class="mb-5">
                        <h6 class="text-uppercase text-primary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">Departmental Information</h6>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Department Name <span class="text-danger">*</span></label>
                            <input type="text" name="dept_name" 
                                   class="form-control form-control-lg border-0 bg-light @error('dept_name') is-invalid @enderror" 
                                   placeholder="e.g., Department of Anatomy" 
                                   value="{{ old('dept_name') }}" required>
                            @error('dept_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 g-md-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Department Code</label>
                                <input type="text" name="dept_code" 
                                       class="form-control border-0 bg-light @error('dept_code') is-invalid @enderror" 
                                       placeholder="e.g., ANAT" 
                                       value="{{ old('dept_code') }}">
                                @error('dept_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Operational Status</label>
                                <select name="is_active" class="form-select border-0 bg-light">
                                    <option value="active" {{ old('is_active') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('is_active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    {{-- Section 3: Leadership --}}
                    <section>
                        <h6 class="text-uppercase text-primary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">Leadership & Location</h6>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Head of Department (HOD)</label>
                            <select id="dept_head_id" name="dept_head_id" class="form-select select2-modern border-0 bg-light">
                                <option value="">Search for staff name...</option>
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold text-dark small">Primary Office Address</label>
                            <textarea name="dept_address" class="form-control border-0 bg-light" rows="3" 
                                      placeholder="Building name, Floor number, Room number...">{{ old('dept_address') }}</textarea>
                        </div>
                    </section>
                </form>
            </div>
        </div>

        {{-- Sidebar Guide: Drops below form on mobile --}}
        <div class="col-lg-4">
            <div class="p-4 rounded-4 mb-4" style="background: #eff6ff; border: 1px solid #dbeafe;">
                <h6 class="fw-bold text-primary mb-3"><i class="fas fa-lightbulb me-2"></i>Quick Tips</h6>
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-3 d-flex">
                        <i class="fas fa-check-circle text-primary me-2 mt-1"></i>
                        <span>The <strong>Department Code</strong> is essential for medical asset tagging and filtering reports.</span>
                    </li>
                    <li class="d-flex">
                        <i class="fas fa-info-circle text-primary me-2 mt-1"></i>
                        <span>Assigning an HOD allows them to approve inventory transfers for this department.</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white rounded-4 shadow-sm p-4 border-0 d-none d-lg-block">
                <h6 class="fw-bold text-dark mb-2 small text-uppercase">Form Checklist</h6>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" checked disabled>
                    <label class="form-check-label small text-muted">Unique Name</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" checked disabled>
                    <label class="form-check-label small text-muted">Faculty Assigned</label>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#dept_head_id').select2({
            placeholder: "Type to search staff...",
            ajax: {
                url: "{{ route('admin.departments.searchHeads') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) { return { q: params.term }; },
                processResults: function(data) { return { results: data }; }
            }
        });
    });
</script>
@endsection