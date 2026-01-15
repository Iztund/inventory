@extends('layouts.admin')

@section('title', 'Create New Unit')

@section('content')
<div class="container-fluid py-5 px-lg-5" style="background-color: #f8fafc; min-height: 100vh;">

    {{-- Header Section --}}
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h4 class="fw-bold mb-0 text-dark" style="letter-spacing: -0.02em;">Create Functional Unit</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 small text-muted bg-transparent p-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.units.index') }}" class="text-decoration-none">Units</a></li>
                    <li class="breadcrumb-item active">New Unit</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.units.index') }}" class="btn btn-white shadow-sm px-4 rounded-3 fw-medium border">
                <i class="fas fa-arrow-left me-2 text-muted"></i>Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="bg-white rounded-4 shadow-sm border-0 overflow-hidden">
                <div class="p-4 border-bottom bg-light bg-opacity-50">
                    <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-plus-circle me-2 text-muted"></i>Enter Unit Details</h6>
                </div>
                
                <div class="p-4 p-lg-5">
                    <form action="{{ route('admin.units.store') }}" method="POST">
                        @csrf

                        {{-- Section 1: Identification --}}
                        <div class="mb-5">
                            <h6 class="fw-bold text-primary text-uppercase mb-4" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                <i class="fas fa-sitemap me-2"></i>Unit Identification & Hierarchy
                            </h6>
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <label for="unit_name" class="form-label small fw-bold text-muted">Unit Name</label>
                                    <input type="text" class="form-control form-control-lg bg-light border-0 @error('unit_name') is-invalid @enderror" 
                                           id="unit_name" name="unit_name" style="font-size: 0.95rem;"
                                           value="{{ old('unit_name') }}" placeholder="e.g. Network Operations Center" required>
                                    @error('unit_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label for="unit_code" class="form-label small fw-bold text-muted">Unit Code</label>
                                    <input type="text" class="form-control form-control-lg bg-light border-0 @error('unit_code') is-invalid @enderror" 
                                           id="unit_code" name="unit_code" style="font-size: 0.95rem;"
                                           value="{{ old('unit_code') }}" placeholder="e.g. NOC-01">
                                    @error('unit_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="office_id" class="form-label small fw-bold text-muted">Parent Office / Department</label>
                                    <select id="office_id" name="office_id" class="form-select form-select-lg bg-light border-0 @error('office_id') is-invalid @enderror" style="font-size: 0.95rem;" required>
                                        <option value="">-- Select Parent Office --</option>
                                        @isset($offices)
                                            @foreach($offices as $office)
                                                <option value="{{ $office->office_id }}" {{ old('office_id') == $office->office_id ? 'selected' : '' }}>
                                                    {{ $office->office_name }} ({{ $office->office_code }})
                                                </option>
                                            @endforeach
                                        @endisset
                                    </select>
                                    @error('office_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="my-5 opacity-25">

                        {{-- Section 2: Leadership --}}
                        <div class="mb-5">
                            <h6 class="fw-bold text-success text-uppercase mb-4" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                                <i class="fas fa-user-shield me-2"></i>Leadership & Operations
                            </h6>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label for="unit_head_id" class="form-label small fw-bold text-muted">Unit Head / Coordinator</label>
                                    <select id="unit_head_id" name="unit_head_id" class="form-select select2-custom @error('unit_head_id') is-invalid @enderror">
                                        <option value="">Search for staff...</option>
                                    </select>
                                    @error('unit_head_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="is_active" class="form-label small fw-bold text-muted">Initial Status</label>
                                    <select class="form-select form-select-lg bg-light border-0 @error('is_active') is-invalid @enderror" id="is_active" name="is_active" style="font-size: 0.95rem;" required>
                                        <option value="active" {{ old('is_active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('is_active') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="unit_location" class="form-label small fw-bold text-muted">Physical Location</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 text-muted"><i class="fas fa-map-marker-alt"></i></span>
                                        <input type="text" class="form-control form-control-lg bg-light border-0 @error('unit_location') is-invalid @enderror" 
                                               id="unit_location" name="unit_location" style="font-size: 0.95rem;"
                                               value="{{ old('unit_location') }}" placeholder="e.g. Building A, Room 302">
                                    </div>
                                    @error('unit_location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="d-flex justify-content-end gap-3 pt-4 border-top mt-5">
                            <a href="{{ route('admin.units.index') }}" class="btn btn-light px-4 rounded-3 fw-medium text-muted">Discard</a>
                            <button type="submit" class="btn btn-dark px-5 rounded-3 fw-bold shadow-sm">
                                <i class="fas fa-check-circle me-2 text-white-50"></i>Create Unit
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Styles and Scripts --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .form-control:focus, .form-select:focus { box-shadow: none !important; border: 1px solid #cbd5e1 !important; background-color: #fff !important; transition: 0.2s ease-in-out; }
    .select2-container--default .select2-selection--single {
        background-color: #f8fafc !important;
        border: none !important;
        height: 48px !important;
        padding: 8px !important;
        border-radius: 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 46px !important; }
    .select2-container--default .select2-selection--single .select2-selection__rendered { color: #1e293b !important; line-height: 28px !important; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#unit_head_id').select2({
            placeholder: 'Type staff name...',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: "{{ route('admin.units.searchHeads') }}",
                dataType: 'json',
                delay: 250,
                data: (params) => ({ q: params.term }),
                processResults: (data) => ({ results: data }),
                cache: true
            }
        });
    });
</script>
@endsection