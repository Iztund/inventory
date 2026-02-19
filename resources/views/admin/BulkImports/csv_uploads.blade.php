@extends('layouts.admin')

@section('title', 'Upload Assets')

@section('content')

<div class="min-vh-100 py-4 px-3 px-lg-5" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);">
<div style="max-width:1200px;" class="mx-auto">

    {{-- Alert System --}}
    @foreach(['success' => ['#059669', 'check-circle'], 'error' => ['#dc2626', 'exclamation-circle'], 'info' => ['#2563eb', 'info-circle']] as $type => $meta)
        @if(session($type))
            <div class="alert alert-dismissible fade show border-0 rounded-4 p-4 mb-4 d-flex align-items-center shadow-lg" 
                 style="background: {{ $meta[0] }}15; border-left: 4px solid {{ $meta[0] }} !important; backdrop-filter: blur(10px); animation: slideDown 0.5s ease-out;">
                
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px; background: {{ $meta[0] }}20;">
                    <i class="fas fa-{{ $meta[1] }} text-white" style="font-size: 1.2rem; color: {{ $meta[0] }}; filter: drop-shadow(0 0 8px {{ $meta[0] }});"></i>
                </div>
                
                <div class="text-white">
                    <div class="fw-bold mb-1" style="font-size: 0.9rem;">{{ ucfirst($type) }}</div>
                    <div style="font-size: 0.85rem; opacity: 0.9;">{{ session($type) }}</div>
                </div>
                
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="alert border-0 rounded-4 p-4 mb-4 d-flex align-items-start shadow-lg" style="background: #dc262615; border-left: 4px solid #dc2626 !important; backdrop-filter: blur(10px);">
            <i class="fas fa-list-ul text-danger fs-5 mt-1 me-3"></i>
            <div>
                <h6 class="fw-bold text-white mb-2">Please fix the following:</h6>
                <ul class="mb-0 ps-3 text-white" style="font-size: 0.85rem; opacity: 0.9;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="text-center mb-5">
        <a href="{{ route('admin.bulk-assets.index') }}" 
           class="btn btn-lg d-inline-flex align-items-center justify-content-center rounded-circle mb-4 shadow-lg" 
           style="width: 60px; height: 60px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); transition: all 0.3s;">
            <i class="fas fa-arrow-left text-white fs-5"></i>
        </a>
        
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" 
             style="width: 120px; height: 120px; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 20px 60px rgba(245,158,11,0.4);">
            <i class="fas fa-cloud-upload-alt text-white" style="font-size: 3rem; filter: drop-shadow(0 0 10px rgba(255,255,255,0.5));"></i>
        </div>
        
        <h1 class="text-white fw-black mb-3" style="font-size: 2.5rem; letter-spacing: -0.02em; text-shadow: 0 10px 30px rgba(0,0,0,0.5);">
            Upload Asset Data
        </h1>
        
        <p class="text-white mb-0" style="font-size: 1.1rem; opacity: 0.8; max-width: 600px; margin: 0 auto;">
            Import multiple assets at once using CSV or Excel files
        </p>
    </div>

    {{-- Instructions Card --}}
    <div class="rounded-4 p-4 mb-4 shadow-lg" style="background: rgba(59, 130, 246, 0.1); backdrop-filter: blur(20px); border: 1px solid rgba(59, 130, 246, 0.2);">
        <div class="d-flex align-items-start gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.2);">
                <i class="fas fa-info-circle text-white fs-4"></i>
            </div>
            <div class="text-white">
                <h6 class="fw-bold mb-3">Quick Start Guide:</h6>
                <ol class="mb-0 ps-3" style="line-height: 1.8;">
                    <li>Download the CSV template below</li>
                    <li>Fill in your asset data following the format</li>
                    <li>Select the target entity (Faculty/Department/Office/Unit/Institute)</li>
                    <li>Upload your completed file</li>
                </ol>
            </div>
        </div>
    </div>

    {{-- Download Template Card --}}
    <div class="rounded-4 p-4 mb-4 shadow-lg d-flex align-items-center justify-content-between" 
         style="background: linear-gradient(135deg, rgba(5, 150, 105, 0.2) 0%, rgba(5, 150, 105, 0.05) 100%); backdrop-filter: blur(20px); border: 1px solid rgba(5, 150, 105, 0.3);">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background: linear-gradient(135deg, #059669 0%, #047857 100%); box-shadow: 0 10px 30px rgba(5,150,105,0.3);">
                <i class="fas fa-file-csv text-white" style="font-size: 2rem;"></i>
            </div>
            <div class="text-white">
                <h5 class="fw-black mb-1" style="font-size: 1.2rem;">CSV Template</h5>
                <p class="mb-0" style="opacity: 0.8; font-size: 0.9rem;">Download template with sample data and column headers</p>
            </div>
        </div>
        <a href="{{ route('admin.bulk-assets.csv.template') }}" 
           class="btn btn-lg rounded-pill px-4 py-3 fw-bold shadow-lg" 
           style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border: none; transition: all 0.3s; position: relative; overflow: hidden;">
            <i class="fas fa-download me-2"></i> Download
        </a>
    </div>

    {{-- Upload Form --}}
    <form action="{{ route('admin.bulk-assets.csv.process') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
        @csrf

        <div class="row g-4">
            
            {{-- Entity Selection --}}
            <div class="col-12">
                <div class="rounded-4 overflow-hidden shadow-lg" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="p-4 d-flex align-items-center gap-3" style="background: rgba(99, 102, 241, 0.1); border-bottom: 1px solid rgba(99, 102, 241, 0.2);">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(99, 102, 241, 0.2);">
                            <i class="fas fa-building text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="text-white fw-black mb-0" style="font-size: 1.2rem;">Target Location</h5>
                            <p class="text-white mb-0" style="opacity: 0.7; font-size: 0.85rem;">Select where these assets will be assigned</p>
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
                                    <i class="fas fa-map-marker-alt me-2"></i> Specific Entity
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

            {{-- File Upload --}}
            <div class="col-12">
                <div class="rounded-4 overflow-hidden shadow-lg" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="p-4 d-flex align-items-center gap-3" style="background: rgba(245, 158, 11, 0.1); border-bottom: 1px solid rgba(245, 158, 11, 0.2);">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; background: rgba(245, 158, 11, 0.2);">
                            <i class="fas fa-file-import text-white" style="font-size: 1.5rem;"></i>
                        </div>
                        <div>
                            <h5 class="text-white fw-black mb-0" style="font-size: 1.2rem;">Upload File</h5>
                            <p class="text-white mb-0" style="opacity: 0.7; font-size: 0.85rem;">Supports CSV, Excel (.xlsx, .xls)</p>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <div class="border-2 border-dashed rounded-4 p-5 text-center" 
                             style="border-color: rgba(255,255,255,0.2) !important; background: rgba(255,255,255,0.02); transition: all 0.3s;"
                             id="dropZone">
                            
                            <input type="file" 
                                   name="import_file" 
                                   id="import_file" 
                                   class="d-none" 
                                   accept=".csv, .xlsx, .xls"
                                   required>
                            
                            <i class="fas fa-cloud-upload-alt text-white mb-4" style="font-size: 4rem; opacity: 0.3;"></i>
                            <h5 class="text-white fw-bold mb-3">Drop your file here or click to browse</h5>
                            <p class="text-white mb-4" style="opacity: 0.6;">Accepts .xlsx, .xls, .csv files up to 10MB</p>
                            
                            <button type="button" 
                                    class="btn btn-lg rounded-pill px-5 py-3 fw-bold shadow-lg" 
                                    style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; border: none;"
                                    onclick="document.getElementById('import_file').click()">
                                <i class="fas fa-folder-open me-2"></i> Browse Files
                            </button>

                            <div id="fileInfo" class="mt-4 d-none">
                                <div class="d-inline-flex align-items-center gap-3 p-3 rounded-pill shadow-lg" 
                                     style="background: rgba(99, 102, 241, 0.2); backdrop-filter: blur(10px); border: 1px solid rgba(99, 102, 241, 0.3);">
                                    <i id="fileIcon" class="fas fa-file-excel text-white" style="font-size: 1.5rem;"></i>
                                    <span id="fileName" class="text-white fw-bold"></span>
                                    <button type="button" class="btn btn-sm btn-link text-white" onclick="clearFile()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Format Reference (Collapsible) --}}
            <div class="col-12">
                <div class="rounded-4 overflow-hidden shadow-lg" style="background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
                    <div class="p-4 d-flex align-items-center justify-content-between" style="background: rgba(59, 130, 246, 0.1); border-bottom: 1px solid rgba(59, 130, 246, 0.2); cursor: pointer;" data-bs-toggle="collapse" data-bs-target="#formatTable">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(59, 130, 246, 0.2);">
                                <i class="fas fa-table text-white"></i>
                            </div>
                            <div>
                                <h5 class="text-white fw-bold mb-0">Expected File Format</h5>
                                <p class="text-white mb-0" style="opacity: 0.7; font-size: 0.85rem;">Click to view column requirements</p>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down text-white"></i>
                    </div>
                    
                    <div class="collapse" id="formatTable">
                        <div class="p-4">
                            <div class="table-responsive">
                                <table class="table table-dark table-hover mb-0" style="background: transparent;">
                                    <thead style="background: rgba(255,255,255,0.05);">
                                        <tr>
                                            <th class="text-white fw-bold border-0 py-3">Column</th>
                                            <th class="text-white fw-bold border-0 py-3">Required</th>
                                            <th class="text-white fw-bold border-0 py-3">Example</th>
                                            <th class="text-white fw-bold border-0 py-3">Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-white fw-bold border-0 py-3">item_name</td>
                                            <td class="border-0 py-3"><span class="badge bg-danger rounded-pill">Required</span></td>
                                            <td class="text-white border-0 py-3" style="opacity: 0.8;">Dell Latitude 5420 Laptop</td>
                                            <td class="text-white border-0 py-3" style="opacity: 0.6;">Full asset name</td>
                                        </tr>
                                        <tr>
                                            <td class="text-white fw-bold border-0 py-3">category_name</td>
                                            <td class="border-0 py-3"><span class="badge bg-danger rounded-pill">Required</span></td>
                                            <td class="text-white border-0 py-3" style="opacity: 0.8;">Electronics</td>
                                            <td class="text-white border-0 py-3" style="opacity: 0.6;">Must match existing</td>
                                        </tr>
                                        <tr>
                                            <td class="text-white fw-bold border-0 py-3">subcategory_name</td>
                                            <td class="border-0 py-3"><span class="badge bg-secondary rounded-pill">Optional</span></td>
                                            <td class="text-white border-0 py-3" style="opacity: 0.8;">Computers</td>
                                            <td class="text-white border-0 py-3" style="opacity: 0.6;">If applicable</td>
                                        </tr>
                                        <tr>
                                            <td class="text-white fw-bold border-0 py-3">quantity</td>
                                            <td class="border-0 py-3"><span class="badge bg-danger rounded-pill">Required</span></td>
                                            <td class="text-white border-0 py-3" style="opacity: 0.8;">1</td>
                                            <td class="text-white border-0 py-3" style="opacity: 0.6;">Number of units</td>
                                        </tr>
                                        <tr>
                                            <td class="text-white fw-bold border-0 py-3">purchase_price</td>
                                            <td class="border-0 py-3"><span class="badge bg-danger rounded-pill">Required</span></td>
                                            <td class="text-white border-0 py-3" style="opacity: 0.8;">450000.00</td>
                                            <td class="text-white border-0 py-3" style="opacity: 0.6;">In Naira</td>
                                        </tr>
                                        <tr>
                                            <td class="text-white fw-bold border-0 py-3">purchase_date</td>
                                            <td class="border-0 py-3"><span class="badge bg-secondary rounded-pill">Optional</span></td>
                                            <td class="text-white border-0 py-3" style="opacity: 0.8;">2024-01-15</td>
                                            <td class="text-white border-0 py-3" style="opacity: 0.6;">YYYY-MM-DD format</td>
                                        </tr>
                                    </tbody>
                                </table>
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
                            style="background: linear-gradient(135deg, #059669 0%, #047857 100%); color: white; border: none; position: relative; overflow: hidden;" 
                            id="submitBtn">
                        <i class="fas fa-upload me-2"></i>
                        <span id="btnText">Upload & Import</span>
                    </button>
                </div>
            </div>

        </div>
    </form>

</div>
</div>

<style>
@keyframes slideDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.form-select:focus,
.btn:hover {
    transform: translateY(-2px);
    transition: all 0.3s;
}

#dropZone:hover {
    border-color: rgba(245, 158, 11, 0.5) !important;
    background: rgba(245, 158, 11, 0.05) !important;
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

// File input
document.getElementById('import_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('fileInfo').classList.remove('d-none');
        
        const iconI = document.getElementById('fileIcon');
        if (file.name.endsWith('.csv')) {
            iconI.className = 'fas fa-file-csv text-white';
        } else {
            iconI.className = 'fas fa-file-excel text-white';
        }
        
        document.getElementById('dropZone').style.borderColor = 'rgba(5, 150, 105, 0.5)';
        document.getElementById('dropZone').style.background = 'rgba(5, 150, 105, 0.1)';
    }
});

function clearFile() {
    document.getElementById('import_file').value = '';
    document.getElementById('fileInfo').classList.add('d-none');
    document.getElementById('dropZone').style.borderColor = 'rgba(255,255,255,0.2)';
    document.getElementById('dropZone').style.background = 'rgba(255,255,255,0.02)';
}

// Form submit
document.getElementById('uploadForm').addEventListener('submit', function() {
    const btn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    
    btn.disabled = true;
    btn.style.opacity = '0.7';
    btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
});

// Drag and drop
const dropZone = document.getElementById('dropZone');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
    });
});

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => {
        dropZone.style.borderColor = 'rgba(5, 150, 105, 0.5)';
        dropZone.style.background = 'rgba(5, 150, 105, 0.1)';
    });
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, () => {
        dropZone.style.borderColor = 'rgba(255,255,255,0.2)';
        dropZone.style.background = 'rgba(255,255,255,0.02)';
    });
});

dropZone.addEventListener('drop', function(e) {
    const files = e.dataTransfer.files;
    document.getElementById('import_file').files = files;
    
    if (files.length > 0) {
        document.getElementById('fileName').textContent = files[0].name;
        document.getElementById('fileInfo').classList.remove('d-none');
    }
});

// Auto-hide alerts
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
        bootstrap.Alert.getOrCreateInstance(alert).close();
    });
}, 5000);
</script>
@endpush

@endsection