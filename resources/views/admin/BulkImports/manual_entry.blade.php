@extends('layouts.admin')

@section('title', 'Add Single Asset')

@section('content')

<div class="min-vh-100 py-4 px-3 px-lg-5 rounded-[50px]" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);">
    <div class="rounded-[40px] border border-white/10 bg-white/5 p-10 shadow-2xl backdrop-blur-[25px]">
        <div style="max-width:1200px;" class="mx-auto">
            <div class="mb-8">
                <a href="{{ route('admin.bulk-assets.index') }}" 
                    class="group relative inline-flex items-center gap-3 overflow-hidden rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-slate-400 no-underline backdrop-blur-md transition-all duration-300 hover:border-amber-500/40 hover:bg-white/10 hover:text-slate-100 hover:-translate-y-1">
                    <i class="fas fa-arrow-left text-sm transition-transform duration-300 group-hover:-translate-x-1 group-hover:text-amber-500"></i>
                    <span class="text-l text-white font-bold tracking-wide">Back</span>
                    <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/5 to-transparent transition-transform duration-500 group-hover:translate-x-full"></div>
                </a>
            </div>
            {{-- Header Section --}}
            <div class="text-center mb-5">
                
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" 
                    style="width: 120px; height: 120px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 20px 60px rgba(245,158,11,0.4);">
                    <i class="fas fa-plus-circle text-white" style="font-size: 3rem; filter: drop-shadow(0 0 10px rgba(255,255,255,0.5));"></i>
                </div>
                
                <h1 class="text-white fw-black mb-3" style="font-size: 2.5rem; letter-spacing: -0.02em; text-shadow: 0 10px 30px rgba(0,0,0,0.5);">
                    Add Single Asset
                </h1>
                
                <p class="text-white mb-0" style="font-size: 1.1rem; opacity: 0.8;">
                    Manually enter asset details for the College of Medicine inventory
                </p>
            </div>

            {{-- Quick Tip --}}
            <div class="rounded-4 p-4 mb-4 shadow-lg" style="background: rgba(59, 130, 246, 0.1); backdrop-filter: blur(20px); border: 1px solid rgba(59, 130, 246, 0.2);">
                <div class="d-flex align-items-start gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.2);">
                        <i class="fas fa-magic text-white fs-4"></i>
                    </div>
                    <div class="text-white">
                        <p class="mb-0"><strong>Quick Tip:</strong> Serial numbers and asset tags are <strong>auto-generated</strong> by the system. Just fill in the details!</p>
                    </div>
                </div>
            </div>

            {{-- Form --}}
            <form action="{{ route('admin.bulk-assets.manual.store') }}" method="POST" id="manualEntryForm">
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
                                    <p class="text-white mb-0" style="opacity: 0.7; font-size: 0.85rem;">Assign to Faculty, Department, Office, Unit, or Institute</p>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label text-white fw-bold mb-2">
                                            <i class="fas fa-sitemap me-2"></i> Entity Type *
                                        </label>
                                        <select name="entity_type" id="entity_type" class="form-select form-select-lg rounded-3 glass-input" required>
                                            <option value="">Select Entity Type</option>
                                            <option value="faculty">Faculty</option>
                                            <option value="office">Office</option>
                                            <option value="department">Department</option>
                                            <option value="unit">Unit</option>
                                            <option value="institute">Institute</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-white fw-bold mb-2">
                                            <i class="fas fa-building me-2"></i> Specific Entity *
                                        </label>
                                        <select name="entity_id" id="entity_id" class="form-select form-select-lg rounded-3 glass-input" required disabled>
                                            <option value="">Select entity type first</option>
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
                                        <select name="category_id" id="category_id" class="form-select form-select-lg rounded-3 glass-input" required>
                                            <option value="">Select Category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->category_id }}">{{ $category->category_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-white fw-bold mb-2">
                                            <i class="fas fa-layer-group me-2"></i> Sub-Category *
                                        </label>
                                        <select name="subcategory_id" id="subcategory_id" class="form-select form-select-lg rounded-3 glass-input" required disabled>
                                            <option value="">Select category first</option>
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
                                        <input type="text" name="item_name" class="form-control form-control-lg rounded-3 glass-input" required 
                                            placeholder="e.g., Dell Latitude 5420 Laptop">
                                    </div>

                                    {{-- Serial Number (Now taking full row or can be combined) --}}
                                    <div class="col-12">
                                        <label class="form-label text-white fw-bold mb-2">
                                            <i class="fas fa-barcode me-2"></i> Serial Number
                                        </label>
                                        <input type="text" name="serial_number" class="form-control form-control-lg rounded-3 glass-input" 
                                            placeholder="Auto-generated if left empty">
                                        <small class="text-white-50" style="font-size: 0.75rem;">Leave empty for system auto-generation</small>
                                    </div>

                                    {{-- Quantity, Price, Date --}}
                                    <div class="col-md-4">
                                        <label class="form-label text-white fw-bold mb-2">
                                            <i class="fas fa-hashtag me-2"></i> Quantity *
                                        </label>
                                        <input type="number" name="quantity" class="form-control form-control-lg rounded-3 glass-input" value="1" min="1" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label text-white fw-bold mb-2">
                                            <i class="fas fa-naira-sign me-2"></i> Unit Price (₦) *
                                        </label>
                                        <input type="number" name="purchase_price" class="form-control form-control-lg rounded-3 glass-input" step="0.01" required 
                                            placeholder="0.00">
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label text-white fw-bold mb-2">
                                            <i class="fas fa-calendar me-2"></i> Purchase Date
                                        </label>
                                        <input type="date" name="purchase_date" class="form-control form-control-lg rounded-3 glass-input">
                                    </div>

                                    {{-- Status & Condition --}}
                                    <div class="col-md-6">
                                        <label class="form-label text-white fw-bold mb-2">
                                            <i class="fas fa-check-circle me-2"></i> Status *
                                        </label>
                                        <select name="status" class="form-select form-select-lg rounded-3 glass-input" required>
                                            <option value="available">Available</option>
                                            <option value="assigned">Assigned</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="retired">Retired</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label text-white fw-bold mb-2">
                                            <i class="fas fa-info-circle me-2"></i> Condition
                                        </label>
                                        <input type="text" name="condition" class="form-control form-control-lg rounded-3 glass-input" 
                                            placeholder="e.g., New, Good, Fair, Poor">
                                    </div>

                                    {{-- Notes --}}
                                    <div class="col-12">
                                        <label class="form-label text-white fw-bold mb-2">
                                            <i class="fas fa-sticky-note me-2"></i> Additional Notes
                                        </label>
                                        <textarea name="notes" class="form-control form-control-lg rounded-3 glass-input" rows="4" 
                                                placeholder="Enter any additional information about this asset..."></textarea>
                                    </div>
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
                                    style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border: none;" 
                                    id="saveBtn">
                                <i class="fas fa-save me-2"></i>
                                <span id="saveBtnText">Register Asset</span>
                            </button>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>
</div>

<style>
/* Glass Input Styling */
.glass-input {
    background: rgba(255,255,255,0.1) !important;
    border: 1px solid rgba(255,255,255,0.2) !important;
    color: white !important;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.glass-input:focus {
    background: rgba(255,255,255,0.15) !important;
    border-color: rgba(245, 158, 11, 0.5) !important;
    color: white !important;
    box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.25) !important;
    transform: translateY(-2px);
}

.glass-input:disabled {
    background: rgba(0,0,0,0.2) !important;
    opacity: 0.5;
    cursor: not-allowed;
}

.glass-input option {
    background: #1e293b;
    color: white;
}

.glass-input::placeholder {
    color: rgba(255,255,255,0.5);
}

/* Date input calendar icon */
input[type="date"]::-webkit-calendar-picker-indicator {
    filter: invert(1);
    cursor: pointer;
}

/* Button hover effects */
.btn:hover {
    transform: translateY(-2px);
    transition: all 0.3s;
}

/* Smooth animations */
.form-select,
.form-control {
    transition: all 0.3s ease;
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
        faculty: @json($faculties ?? []),
        department: @json($departments ?? []),
        office: @json($offices ?? []),
        unit: @json($units ?? []),
        institute: @json($institutes ?? [])
    };

    const options = entities[entityType];
    let html = '<option value="">Select ' + entityType + '</option>';
    
    options.forEach(item => {
        const idField = entityType + '_id';
        const nameField = (entityType === 'department' ? 'dept' : entityType) + '_name';
        html += `<option value="${item[idField]}">${item[nameField]}</option>`;
    });

    entityIdSelect.innerHTML = html;
    entityIdSelect.disabled = false;
});

// Category dropdown - FIXED
document.getElementById('category_id').addEventListener('change', function() {
    const categoryId = this.value;
    const subcategorySelect = document.getElementById('subcategory_id');
    
    subcategorySelect.innerHTML = '<option value="">Loading...</option>';
    subcategorySelect.disabled = true;

    if (!categoryId) {
        subcategorySelect.innerHTML = '<option value="">Select category first</option>';
        return;
    }

    const subcategories = @json($subcategories ?? []);
    const filtered = subcategories.filter(sub => sub.category_id == categoryId);
    
    let html = '<option value="">Select sub-category</option>';
    
    if (filtered.length > 0) {
        filtered.forEach(sub => {
            html += `<option value="${sub.subcategory_id}">${sub.subcategory_name}</option>`;
        });
        subcategorySelect.innerHTML = html;
        subcategorySelect.disabled = false;
    } else {
        subcategorySelect.innerHTML = '<option value="">No subcategories found</option>';
        subcategorySelect.disabled = true;
    }
});

// Form submit
document.getElementById('manualEntryForm').addEventListener('submit', function() {
    const btn = document.getElementById('saveBtn');
    const btnText = document.getElementById('saveBtnText');
    
    btn.disabled = true;
    btn.style.opacity = '0.7';
    btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
});
</script>
@endpush

@endsection