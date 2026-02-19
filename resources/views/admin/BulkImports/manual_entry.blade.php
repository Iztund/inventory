@extends('layouts.admin')

@section('title', 'Add Single Asset')

@section('content')

<div class="min-vh-100 py-4 px-3 px-lg-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);">
<div style="max-width:1200px;" class="mx-auto">

    {{-- Header Section --}}
    <div class="text-center mb-5">
        <a href="{{ route('admin.bulk-assets.index') }}" 
           class="btn btn-lg d-inline-flex align-items-center justify-content-center rounded-circle mb-4 shadow-lg" 
           style="width: 60px; height: 60px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s;">
            <i class="fas fa-arrow-left text-white fs-5"></i>
        </a>
        
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" 
             style="width: 120px; height: 120px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 20px 60px rgba(245,158,11,0.4);">
            <i class="fas fa-plus-circle text-white" style="font-size: 3rem; filter: drop-shadow(0 0 10px rgba(255,255,255,0.5));"></i>
        </div>
        
        <h1 class="text-white fw-black mb-3" style="font-size: 2.5rem; letter-spacing: -0.02em; text-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            Add Single Asset
        </h1>
        
        <p class="text-white mb-0" style="font-size: 1.1rem; opacity: 0.8; max-width: 600px; margin: 0 auto;">
            Manually enter asset details for quick single-item additions
        </p>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.bulk-assets.manual.store') }}" method="POST" id="assetForm">
        @csrf

        <div class="row g-4">
            
            {{-- Entity Location Card --}}
            <div class="col-12">
                <div class="rounded-4 overflow-hidden shadow-lg" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="p-4 d-flex align-items-center gap-3" style="background: rgba(99, 102, 241, 0.1); border-bottom: 1px solid rgba(99, 102, 241, 0.2);">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(99, 102, 241, 0.2);">
                            <i class="fas fa-map-marker-alt text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="text-white fw-black mb-0" style="font-size: 1.2rem;">Asset Location</h5>
                            <p class="text-white mb-0" style="opacity: 0.7; font-size: 0.85rem;">Where will this asset be assigned?</p>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-white fw-bold mb-2">
                                    <i class="fas fa-sitemap me-2"></i> Entity Type
                                </label>
                                <select name="entity_type" id="entity_type" class="form-select form-select-lg rounded-3" required
                                        style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">
                                    <option value="" style="background: #1e293b; color: white;">Select Entity Type</option>
                                    <option value="faculty" style="background: #1e293b; color: white;">Faculty</option>
                                    <option value="department" style="background: #1e293b; color: white;">Department</option>
                                    <option value="office" style="background: #1e293b; color: white;">Office</option>
                                    <option value="unit" style="background: #1e293b; color: white;">Unit</option>
                                    <option value="institute" style="background: #1e293b; color: white;">Institute</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white fw-bold mb-2">
                                    <i class="fas fa-building me-2"></i> Specific Entity
                                </label>
                                <select name="entity_id" id="entity_id" class="form-select form-select-lg rounded-3" required disabled
                                        style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">
                                    <option value="" style="background: #1e293b; color: white;">Select entity type first</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Asset Classification Card --}}
            <div class="col-12">
                <div class="rounded-4 overflow-hidden shadow-lg" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="p-4 d-flex align-items-center gap-3" style="background: rgba(5, 150, 105, 0.1); border-bottom: 1px solid rgba(5, 150, 105, 0.2);">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(5, 150, 105, 0.2);">
                            <i class="fas fa-tags text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="text-white fw-black mb-0" style="font-size: 1.2rem;">Asset Classification</h5>
                            <p class="text-white mb-0" style="opacity: 0.7; font-size: 0.85rem;">Categorize this asset</p>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-white fw-bold mb-2">
                                    <i class="fas fa-folder me-2"></i> Category *
                                </label>
                                <select name="category_id" id="category_id" class="form-select form-select-lg rounded-3" required
                                        style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">
                                    <option value="" style="background: #1e293b; color: white;">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}" style="background: #1e293b; color: white;">{{ $category->category_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white fw-bold mb-2">
                                    <i class="fas fa-layer-group me-2"></i> Sub-Category
                                </label>
                                <select name="subcategory_id" id="subcategory_id" class="form-select form-select-lg rounded-3"
                                        style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">
                                    <option value="" style="background: #1e293b; color: white;">Select category first</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Asset Details Card --}}
            <div class="col-12">
                <div class="rounded-4 overflow-hidden shadow-lg" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="p-4 d-flex align-items-center gap-3" style="background: rgba(245, 158, 11, 0.1); border-bottom: 1px solid rgba(245, 158, 11, 0.2);">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(245, 158, 11, 0.2);">
                            <i class="fas fa-box text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="text-white fw-black mb-0" style="font-size: 1.2rem;">Asset Details</h5>
                            <p class="text-white mb-0" style="opacity: 0.7; font-size: 0.85rem;">Comprehensive asset information</p>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <div class="row g-3">
                            {{-- Item Name --}}
                            <div class="col-12">
                                <label class="form-label text-white fw-bold mb-2">
                                    <i class="fas fa-cube me-2"></i> Item Name *
                                </label>
                                <input type="text" name="item_name" class="form-control form-control-lg rounded-3" required 
                                       placeholder="e.g., Dell Latitude 5420 Laptop"
                                       style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">
                            </div>

                            {{-- Serial Number & Asset Tag --}}
                            <div class="col-md-6">
                                <label class="form-label text-white fw-bold mb-2">
                                    <i class="fas fa-barcode me-2"></i> Serial Number
                                </label>
                                <input type="text" name="serial_number" class="form-control form-control-lg rounded-3" 
                                       placeholder="Auto-generated if empty"
                                       style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">
                                <small class="text-white" style="opacity: 0.5; font-size: 0.75rem;">Leave empty for auto-generation</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white fw-bold mb-2">
                                    <i class="fas fa-tag me-2"></i> Asset Tag
                                </label>
                                <input type="text" name="asset_tag" class="form-control form-control-lg rounded-3" 
                                       placeholder="System will generate"
                                       style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">
                                <small class="text-white" style="opacity: 0.5; font-size: 0.75rem;">Format: COM/ENTITY/CAT/SUBCAT/YY/XXXXXX</small>
                            </div>

                            {{-- Quantity, Price, Date --}}
                            <div class="col-md-4">
                                <label class="form-label text-white fw-bold mb-2">
                                    <i class="fas fa-hashtag me-2"></i> Quantity *
                                </label>
                                <input type="number" name="quantity" class="form-control form-control-lg rounded-3" value="1" min="1" required
                                       style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-white fw-bold mb-2">
                                    <i class="fas fa-naira-sign me-2"></i> Purchase Price (₦) *
                                </label>
                                <input type="number" name="purchase_price" class="form-control form-control-lg rounded-3" step="0.01" required 
                                       placeholder="0.00"
                                       style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label text-white fw-bold mb-2">
                                    <i class="fas fa-calendar me-2"></i> Purchase Date
                                </label>
                                <input type="date" name="purchase_date" class="form-control form-control-lg rounded-3"
                                       style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">
                            </div>

                            {{-- Status & Condition --}}
                            <div class="col-md-6">
                                <label class="form-label text-white fw-bold mb-2">
                                    <i class="fas fa-check-circle me-2"></i> Status *
                                </label>
                                <select name="status" class="form-select form-select-lg rounded-3" required
                                        style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">
                                    <option value="available" style="background: #1e293b; color: white;">Available</option>
                                    <option value="assigned" style="background: #1e293b; color: white;">Assigned</option>
                                    <option value="maintenance" style="background: #1e293b; color: white;">Maintenance</option>
                                    <option value="retired" style="background: #1e293b; color: white;">Retired</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-white fw-bold mb-2">
                                    <i class="fas fa-info-circle me-2"></i> Condition
                                </label>
                                <input type="text" name="condition" class="form-control form-control-lg rounded-3" 
                                       placeholder="e.g., New, Good, Fair, Poor"
                                       style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);">
                            </div>

                            {{-- Notes --}}
                            <div class="col-12">
                                <label class="form-label text-white fw-bold mb-2">
                                    <i class="fas fa-sticky-note me-2"></i> Additional Notes
                                </label>
                                <textarea name="notes" class="form-control form-control-lg rounded-3" rows="4" 
                                          placeholder="Enter any additional information about this asset..."
                                          style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; backdrop-filter: blur(10px);"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Card --}}
            <div class="col-12">
                <div class="rounded-4 p-4 shadow-lg" style="background: rgba(59, 130, 246, 0.1); backdrop-filter: blur(20px); border: 1px solid rgba(59, 130, 246, 0.2);">
                    <div class="d-flex align-items-start gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 40px; height: 40px; background: rgba(59, 130, 246, 0.2);">
                            <i class="fas fa-lightbulb text-white"></i>
                        </div>
                        <div class="text-white" style="font-size: 0.85rem;">
                            <p class="mb-2"><strong>Quick Tips:</strong></p>
                            <ul class="mb-0 ps-3" style="opacity: 0.8; line-height: 1.6;">
                                <li>Serial numbers and asset tags will be auto-generated if left empty</li>
                                <li>Asset tags follow format: COM/ENTITY/CAT/SUBCAT/YY/XXXXXX</li>
                                <li>All fields marked with * are required</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="col-12">
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('admin.bulk-assets.index') }}" 
                       class="btn btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg" 
                       style="background: rgba(255,255,255,0.1); color: white; border: 1px solid rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                        <i class="fas fa-times me-2"></i> Cancel
                    </a>
                    
                    <button type="submit" 
                            class="btn btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg" 
                            style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border: none; position: relative; overflow: hidden;" 
                            id="submitBtn">
                        <i class="fas fa-save me-2"></i>
                        <span id="btnText">Add Asset</span>
                    </button>
                </div>
            </div>

        </div>
    </form>

</div>
</div>

<style>
.form-control:focus,
.form-select:focus {
    background: rgba(255,255,255,0.15) !important;
    border-color: rgba(245, 158, 11, 0.5) !important;
    color: white !important;
    box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.25) !important;
}

.form-control::placeholder {
    color: rgba(255,255,255,0.5);
}

.btn:hover {
    transform: translateY(-2px);
    transition: all 0.3s;
}

/* Date input styling */
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}
</style>

@push('scripts')
<script>
// Entity type dropdown
document.getElementById('entity_type').addEventListener('change', function() {
    const entityType = this.value;
    const entityIdSelect = document.getElementById('entity_id');
    
    entityIdSelect.innerHTML = '<option value="">Loading...</option>';
    entityIdSelect.disabled = true;

    if (!entityType) {
        entityIdSelect.innerHTML = '<option value="">Select entity type first</option>';
        return;
    }

    const entities = {
        faculty: @json($faculties),
        department: @json($departments),
        office: @json($offices),
        unit: @json($units),
        institute: @json($institutes)
    };

    const options = entities[entityType];
    let html = '<option value="" style="background: #1e293b; color: white;">Select ' + entityType + '</option>';
    
    options.forEach(item => {
        const idField = entityType + '_id';
        const nameField = (entityType === 'department' ? 'dept' : entityType) + '_name';
        html += `<option value="${item[idField]}" style="background: #1e293b; color: white;">${item[nameField]}</option>`;
    });

    entityIdSelect.innerHTML = html;
    entityIdSelect.disabled = false;
});

// Category dropdown
document.getElementById('category_id').addEventListener('change', function() {
    const categoryId = this.value;
    const subcategorySelect = document.getElementById('subcategory_id');
    
    subcategorySelect.innerHTML = '<option value="" style="background: #1e293b; color: white;">Select sub-category</option>';

    if (!categoryId) return;

    const subcategories = @json($subcategories);
    const filtered = subcategories.filter(sub => sub.category_id == categoryId);
    
    filtered.forEach(sub => {
        subcategorySelect.innerHTML += `<option value="${sub.subcategory_id}" style="background: #1e293b; color: white;">${sub.subcategory_name}</option>`;
    });
});

// Form submit
document.getElementById('assetForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    
    btn.disabled = true;
    btn.style.opacity = '0.7';
    btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
});
</script>
@endpush

@endsection