@extends('layouts.admin')

@section('title', 'Edit Faculty')

@section('content')
<div class="container-fluid py-5 px-lg-5" style="background-color: #f8fafc; min-height: 100vh;">
    
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.faculties.index') }}" class="btn btn-white shadow-sm rounded-3 me-3 border-0" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; background: white;">
                <i class="fas fa-chevron-left text-muted"></i>
            </a>
            <div>
                <h4 class="fw-bold mb-0 text-dark">Edit Faculty: {{ $faculty->faculty_code }}</h4>
                <span class="text-muted small">Update {{ $faculty->faculty_name }} details</span>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.faculties.index') }}" class="btn btn-link text-decoration-none text-muted me-3 fw-medium">Cancel</a>
            <button type="submit" form="facultyForm" class="btn btn-primary px-4 py-2 rounded-3 fw-medium shadow-sm">
                Update Faculty
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white rounded-4 shadow-sm p-5 border-0">
                <form action="{{ route('admin.faculties.update', $faculty->faculty_id) }}" method="POST" id="facultyForm">
                    @csrf
                    @method('PUT')

                    <section class="mb-5">
                        <h6 class="text-uppercase text-primary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">General Information</h6>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Faculty Name</label>
                            <input type="text" name="faculty_name" 
                                   class="form-control form-control-lg border-0 bg-light @error('faculty_name') is-invalid @enderror" 
                                   value="{{ old('faculty_name', $faculty->faculty_name) }}" required>
                            @error('faculty_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Faculty Code</label>
                                <input type="text" name="faculty_code" 
                                       class="form-control border-0 bg-light @error('faculty_code') is-invalid @enderror" 
                                       value="{{ old('faculty_code', $faculty->faculty_code) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold text-dark small">Operational Status</label>
                                <select name="is_active" class="form-select border-0 bg-light">
                                    <option value="active" {{ $faculty->is_active == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $faculty->is_active == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <section>
                        <h6 class="text-uppercase text-primary fw-bold mb-4" style="font-size: 0.75rem; letter-spacing: 0.1em;">Leadership & Location</h6>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark small">Dean of Faculty</label>
                            <select id="faculty_dean_id" name="faculty_dean_id" class="form-select select2-modern border-0 bg-light">
                                @if($faculty->dean)
                                    <option value="{{ $faculty->faculty_dean_id }}" selected>
                                        {{ $faculty->dean->profile->full_name ?? $faculty->dean->username }} ({{ $faculty->dean->email }})
                                    </option>
                                @endif
                            </select>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-semibold text-dark small">Faculty Secretariat Address</label>
                            <textarea name="faculty_address" class="form-control border-0 bg-light" rows="3">{{ old('faculty_address', $faculty->faculty_address) }}</textarea>
                        </div>
                    </section>
                </form>
            </div>
        </div>
    </div>
</div>

@include('admin.manage_faculties.partials.faculty_scripts')
@endsection