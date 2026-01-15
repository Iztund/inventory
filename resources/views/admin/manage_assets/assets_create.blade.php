@extends('layouts.admin')

@section('title', 'Add New Asset')
@section('active_link', 'assets')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* --- BEAUTIFIED UI STYLES --- */
    body { 
        background: linear-gradient(135deg, #f8fafc 0%, #eff6ff 100%); 
        min-height: 100vh;
    }
    .form-section-title {
        font-size: 0.75rem;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 10px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
    }
    .form-section-title i { color: #3b82f6; margin-right: 10px; }

    .card-custom {
        border: none;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04);
    }

    .form-label-custom {
        font-size: 0.7rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 8px;
        display: block;
    }

    .form-control, .form-select, .select2-container--default .select2-selection--single {
        height: 44px !important;
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        background-color: #fcfdfe !important;
        font-size: 0.85rem !important;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important;
        background-color: #fff !important;
    }

    /* File Upload Area */
    .file-upload-wrapper {
        border: 2px dashed #cbd5e1;
        border-radius: 15px;
        padding: 30px;
        text-align: center;
        background: #f8fafc;
        transition: all 0.3s ease;
        position: relative;
    }
    .file-upload-wrapper:hover {
        border-color: #3b82f6;
        background: #f0f7ff;
    }
    .file-input-hidden {
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        cursor: pointer;
    }

    #file-preview-zone {
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4 px-lg-5">
    
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1 text-dark">Enroll New Asset</h4>
            <p class="text-muted small mb-0">Register a new physical asset into the university central registry.</p>
        </div>
        <a href="{{ route('admin.assets.index') }}" class="btn btn-white bg-white border px-4 rounded-3 shadow-sm small fw-bold">
            <i class="fas fa-chevron-left me-2 text-primary"></i>Back to Inventory
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="{{ route('admin.assets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- 1. General Info --}}
                <div class="card card-custom mb-4">
                    <div class="card-body p-4">
                        <div class="form-section-title"><i class="fas fa-tag"></i>Identity & Categorization</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Product Name <span class="text-danger">*</span></label>
                                <input type="text" name="product_name" class="form-control @error('product_name') is-invalid @enderror" value="{{ old('product_name') }}" placeholder="e.g. Dell Precision 5570" required>
                                @error('product_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Serial / Asset ID</label>
                                <input type="text" name="serial_number" class="form-control @error('serial_number') is-invalid @enderror" value="{{ old('serial_number') }}" placeholder="SN-XXXX-XXXX">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Asset Category <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Select a Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}" {{ old('category_id') == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-custom">Current Status</label>
                                <select name="status" class="form-select">
                                    <option value="available" selected>Available (In Stock)</option>
                                    <option value="assigned">Assigned (In Use)</option>
                                    <option value="maintenance">Maintenance (Repair)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 2. Financial & Meta --}}
                <div class="card card-custom mb-4">
                    <div class="card-body p-4">
                        <div class="form-section-title"><i class="fas fa-file-invoice-dollar"></i>Procurement & Value</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label-custom">Acquisition Date</label>
                                <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date', now()->toDateString()) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">Unit Cost (₦)</label>
                                <input type="number" name="unit_cost" class="form-control" step="0.01" value="{{ old('unit_cost', 0) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">Initial Quantity</label>
                                <input type="number" name="quantity" class="form-control" value="{{ old('quantity', 1) }}" min="1">
                            </div>
                            <div class="col-12">
                                <label class="form-label-custom">Technical Description</label>
                                <textarea name="description" class="form-control" rows="2" placeholder="Describe the item's condition or specifications...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Ownership Path --}}
                <div class="card card-custom mb-4">
                    <div class="card-body p-4">
                        <div class="form-section-title"><i class="fas fa-sitemap"></i>Custody Assignment</div>
                        <p class="text-muted small mb-3">Assign this asset to a specific organizational branch:</p>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label-custom">Admin Unit</label>
                                <select name="unit_id" class="form-select select2-units"></select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">Academic Dept</label>
                                <select name="dept_id" class="form-select select2-depts"></select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label-custom">Research Institute</label>
                                <select name="institute_id" class="form-select select2-institutes"></select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. Media & Upload with LIVE PREVIEW --}}
                <div class="card card-custom mb-4">
                    <div class="card-body p-4">
                        <div class="form-section-title"><i class="fas fa-camera"></i>Evidence & Documentation</div>
                        <div class="file-upload-wrapper">
                            <input type="file" name="asset_file" class="file-input-hidden" id="asset_file" accept="image/*,application/pdf">
                            <div id="upload-placeholder">
                                <i class="fas fa-cloud-upload-alt text-primary mb-2" style="font-size: 2rem;"></i>
                                <h6 class="fw-bold text-dark mb-1">Click to upload asset file</h6>
                                <p class="text-muted small mb-0">Drag and drop invoice or photo (Max 5MB)</p>
                            </div>
                            {{-- Preview Zone --}}
                            <div id="file-preview-zone" class="d-none">
                                <div class="d-flex align-items-center justify-content-center p-3 bg-white rounded-3 shadow-sm border">
                                    <img id="img-preview" src="#" class="rounded me-3 d-none" style="width: 50px; height: 50px; object-fit: cover;">
                                    <div id="pdf-icon" class="me-3 d-none text-danger"><i class="fas fa-file-pdf fa-2x"></i></div>
                                    <div class="text-start">
                                        <div id="file-name-display" class="fw-bold text-dark small"></div>
                                        <div id="file-size-display" class="text-muted small"></div>
                                    </div>
                                    <button type="button" id="remove-file" class="btn btn-sm btn-link text-danger ms-auto"><i class="fas fa-times-circle"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-3 mb-5">
                    <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 fw-bold shadow-sm">
                        <i class="fas fa-save me-2"></i>Create Asset Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // --- 1. LIVE PREVIEW ENGINE ---
    $('#asset_file').on('change', function() {
        const file = this.files[0];
        if (file) {
            $('#upload-placeholder').addClass('d-none');
            $('#file-preview-zone').removeClass('d-none');
            $('#file-name-display').text(file.name);
            $('#file-size-display').text((file.size / 1024).toFixed(1) + ' KB');

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = e => {
                    $('#img-preview').attr('src', e.target.result).removeClass('d-none');
                    $('#pdf-icon').addClass('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                $('#pdf-icon').removeClass('d-none');
                $('#img-preview').addClass('d-none');
            }
        }
    });

    $('#remove-file').on('click', function() {
        $('#asset_file').val('');
        $('#file-preview-zone').addClass('d-none');
        $('#upload-placeholder').removeClass('d-none');
    });

    // --- 2. SELECT2 INITIALIZATION ---
    const setupSelect2 = (selector, url, placeholder) => {
        $(selector).select2({
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,
                data: params => ({ search: params.term }),
                processResults: data => ({ results: data.results || data })
            },
            placeholder: placeholder,
            allowClear: true,
            width: '100%'
        });
    };

    setupSelect2('.select2-depts', "{{ route('admin.departments.searchDepartments') }}", "Select Department");
    setupSelect2('.select2-institutes', "{{ route('admin.institutes.search') }}", "Select Institute");
    setupSelect2('.select2-units', "{{ route('admin.units.searchHeads') }}", "Select Admin Unit");

    // Ownership Mutual Exclusion
    $('.select2-depts, .select2-units, .select2-institutes').on('select2:select', function () {
        $('.select2-depts, .select2-units, .select2-institutes').not(this).val(null).trigger('change');
    });
});
</script>
@endpush