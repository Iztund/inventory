@extends('layouts.admin')

@section('title', 'Edit Department | ' . $department->dept_name)

@section('content')
<div class="container-fluid py-5 px-lg-5" style="background-color: #f8fafc; min-height: 100vh;">
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.departments.index') }}" 
               class="btn btn-white shadow-sm rounded-3 me-3 border-0" 
               style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: white;">
                <i class="fas fa-chevron-left text-muted" style="font-size: 0.9rem;"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">Edit Department</h4>
                <span class="text-muted small">Update details for <strong>{{ $department->dept_name }}</strong></span>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.departments.index') }}" class="btn btn-link text-decoration-none text-muted me-3 fw-medium">Cancel</a>
            <button type="submit" form="editDeptForm" class="btn btn-primary px-4 py-2 rounded-3 fw-medium shadow-sm">
                Update Department
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white rounded-4 shadow-sm p-5 border-0">
                <form action="{{ route('admin.departments.update', $department->dept_id) }}" method="POST" id="editDeptForm">
                    @csrf
                    @method('PUT')

                    <section class="mb-5">
                        <h6 class="text-uppercase text-primary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">Institutional Hierarchy</h6>
                        
                        {{-- PARENT FACULTY SELECTION --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Parent Faculty</label>
                            <select name="faculty_id" class="form-select border-0 bg-light @error('faculty_id') is-invalid @enderror" required>
                                <option value="" disabled>-- Select Faculty --</option>
                                @foreach($faculties as $faculty)
                                    <option value="{{ $faculty->faculty_id }}" 
                                        {{ (old('faculty_id', $department->faculty_id) == $faculty->faculty_id) ? 'selected' : '' }}>
                                        {{ $faculty->faculty_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('faculty_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </section>

                    <section class="mb-5">
                        <h6 class="text-uppercase text-primary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">Department Details</h6>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Department Name</label>
                            <input type="text" name="dept_name" 
                                   class="form-control form-control-lg border-0 bg-light @error('dept_name') is-invalid @enderror" 
                                   value="{{ old('dept_name', $department->dept_name) }}" required>
                            @error('dept_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Department Code</label>
                                <input type="text" name="dept_code" 
                                       class="form-control border-0 bg-light @error('dept_code') is-invalid @enderror" 
                                       value="{{ old('dept_code', $department->dept_code) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Operational Status</label>
                                <select name="is_active" class="form-select border-0 bg-light">
                                    <option value="active" {{ old('is_active', $department->is_active) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('is_active', $department->is_active) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h6 class="text-uppercase text-primary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">Leadership & Location</h6>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Head of Department</label>
                            <select id="dept_head_id" name="dept_head_id" class="form-select select2-modern border-0 bg-light">
                                @if($department->head)
                                    <option value="{{ $department->dept_head_id }}" selected>{{ $department->head->name }}</option>
                                @endif
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold text-dark small">Physical Address / Location</label>
                            <textarea name="dept_address" class="form-control border-0 bg-light" rows="3">{{ old('dept_address', $department->dept_address) }}</textarea>
                        </div>
                    </section>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- STATUS CARD --}}
            <div class="bg-white rounded-4 shadow-sm p-4 mb-4 border-0">
                <h6 class="fw-bold text-dark mb-3 small text-uppercase">Asset Summary</h6>
                <div class="d-flex align-items-center mb-3">
                    <div class="icon-circle bg-light-primary text-primary me-3">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div>
                        <p class="text-muted small mb-0">Total Assets</p>
                        <h5 class="fw-bold mb-0">{{ $department->assets_count ?? 0 }} Items</h5>
                    </div>
                </div>
            </div>

            <div class="p-4 rounded-4" style="background: #fff8eb; border: 1px solid #ffeeba;">
                <h6 class="fw-bold text-warning mb-3">Important Note</h6>
                <p class="small text-muted mb-0">
                    Changing the <strong>Status</strong> to Inactive will hide this department from the active inventory lists, but all historical asset records will be preserved for audit purposes.
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control, .form-select {
        padding: 0.75rem 1rem;
        border-radius: 10px;
    }
    .form-control:focus, .form-select:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.1);
        border: 1px solid #0d6efd !important;
    }
    .icon-circle {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .bg-light-primary { background-color: #f0f7ff; }
</style>

{{-- Select2 Script --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#dept_head_id').select2({
            placeholder: "Search for a staff member...",
            ajax: {
                url: "{{ route('admin.departments.searchHeads') }}",
                dataType: 'json',
                delay: 250,
                processResults: function(data) { return { results: data }; }
            }
        });
    });
</script>
@endsection