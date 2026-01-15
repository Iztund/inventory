@extends('layouts.staff')

@section('title', 'Inventory Audit Submission')

@section('content')
<style>
    :root {
        --med-navy: #0f172a; --med-blue: #3b82f6; --med-crimson: #ef4444;
        --med-slate: #f8fafc; --med-border: #e2e8f0; --med-accent: #7dd3fc;
    }
    body { background-color: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.82rem; }
    
    .audit-card { border: none; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); margin-bottom: 1.5rem; background: #fff; }
    .item-row { background: #fff; border: 1.5px solid var(--med-border) !important; border-radius: 12px; position: relative; margin-bottom: 2rem; overflow: hidden; }
    .item-header { background: #f8fafc; border-bottom: 1px solid var(--med-border); padding: 12px 20px; display: flex; justify-content: space-between; align-items: center; }
    .admin-box { background: #f0f7ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
    
    .form-control, .form-select { font-size: 0.85rem; border-radius: 8px; border: 1.2px solid var(--med-border); }
    .form-label { font-size: 0.72rem; font-weight: 700; color: #475569; }

    .file-preview-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; margin-top: 10px; }
    .preview-item { position: relative; border: 1px solid var(--med-border); border-radius: 8px; padding: 5px; background: #fff; text-align: center; font-size: 10px; min-height: 85px; }
    .preview-item img { width: 100%; height: 60px; object-fit: cover; border-radius: 5px; }
    .remove-file { position: absolute; top: -5px; right: -5px; background: var(--med-crimson); color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; font-weight: bold; }
    
    .sidebar-meta { background: var(--med-navy); color: white; border-radius: 20px; padding: 25px; }
    .side-label { font-size: 0.62rem; font-weight: 800; color: var(--med-accent); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 6px; }
    .entity-tag { background: rgba(255,255,255,0.05); border-left: 3px solid var(--med-accent); padding: 8px 12px; margin-bottom: 8px; border-radius: 0 6px 6px 0; font-size: 0.75rem; color: #fff; }

    /* RESPONSIVE OVERRIDES */
    @media (max-width: 991.98px) {
        .sticky-top { position: static !important; }
        .sidebar-meta { margin-top: 1rem; margin-bottom: 3rem; }
    }
    @media (max-width: 575.98px) {
        .item-header { flex-direction: column; align-items: flex-start; gap: 8px; }
        .admin-box { padding: 12px; }
        .p-4 { padding: 1.5rem !important; }
    }
</style>

<div class="container-fluid px-2 px-lg-4">
    {{-- Header Section: Stacks on mobile, row on desktop --}}
    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between mb-4 mt-4 gap-3">
        <div>
            <h1 class="h5 fw-bold text-navy mb-0" style="color: var(--med-navy);">Inventory Audit Submission</h1>
            <p class="text-muted mb-0" style="font-size: 0.7rem;">College of Medicine Inventory System</p>
        </div>
        <a href="{{ route('staff.submissions.index') }}" class="btn btn-sm btn-white border rounded-pill px-4 shadow-sm fw-bold">
            <i class="fas fa-arrow-left me-1"></i> Back
        </a>
    </div>

    <form action="{{ route('staff.submissions.store') }}" method="POST" enctype="multipart/form-data" id="audit-form">
        @csrf
        <div class="row">
            {{-- LEFT SIDE: ITEMS (Full width on mobile/tablet, 8/12 on desktop) --}}
            <div class="col-lg-8 order-2 order-lg-1">
                <div id="items-container">
                    <div class="item-row shadow-sm" data-row-index="0">
                        <div class="item-header">
                            <span class="fw-bold text-primary item-number-label small text-uppercase"><i class="fas fa-box me-2"></i>Asset Entry #1</span>
                        </div>
                        
                        <div class="p-4">
                            <div class="admin-box">
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <label class="form-label">Entry Type</label>
                                        <select name="items[0][submission_type]" class="form-select" onchange="toggleRowFields(this)">
                                            <option value="new_purchase">New Purchase</option>
                                            <option value="transfer">Internal Transfer</option>
                                            <option value="disposal">Disposal</option>
                                            <option value="audit">Existing Audit/Update</option>
                                        </select>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Item Funding Source</label>
                                        <input type="text" name="items[0][funding_source_per_item]" class="form-control" placeholder="Specific source (e.g. TETFUND)">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label text-primary">Item Specific Notes</label>
                                        <textarea name="items[0][item_notes]" rows="2" class="form-control" placeholder="Technical details or specific item condition..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <label class="form-label">Category</label>
                                    <select name="items[0][category_id]" class="form-select category-select" onchange="updateSubcats(this)" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->category_id }}">{{ $cat->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label class="form-label">Sub-Category</label>
                                    <select name="items[0][subcategory_id]" class="form-select subcategory-select" required>
                                        <option value="">-- Select Category First --</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Item Name / Model Description</label>
                                    <input type="text" name="items[0][item_name]" class="form-control" required>
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" name="items[0][quantity]" value="1" min="1" class="form-control qty-input" oninput="runGrandTotal()">
                                </div>
                                <div class="col-6 col-md-4 cost-area">
                                    <label class="form-label">Unit Cost (₦)</label>
                                    <input type="number" step="0.01" name="items[0][cost]" class="form-control cost-input" oninput="runGrandTotal()">
                                </div>
                                <div class="col-12 col-md-5 serial-area">
                                    <label class="form-label">Serial Number</label>
                                    <input type="text" name="items[0][serial_number]" class="form-control">
                                </div>

                                <div class="col-12 mt-3">
                                    <label class="form-label text-primary">Supporting Evidence (Photos/PDFs)</label>
                                    <div class="p-3 border rounded-3 bg-light">
                                        <input type="file" class="form-control form-control-sm" multiple onchange="handleFileSelect(this)">
                                        <div class="hidden-inputs-container" style="display: none;"></div>
                                        <div class="file-preview-container"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-row" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm mb-5 w-100 w-sm-auto">
                    <i class="fas fa-plus-circle me-2 text-primary"></i> Add Another Asset
                </button>
            </div>

            {{-- RIGHT SIDE: SUMMARY (Appears first on mobile for quick visibility of Total) --}}
            <div class="col-lg-4 order-1 order-lg-2">
                <div class="sticky-top" style="top: 85px; z-index: 10;">
                    <div class="sidebar-meta shadow-sm">
                        <div class="text-center mb-4">
                            <h6 class="side-label opacity-75">Total Submission Value</h6>
                            <h3 class="fw-bold mb-0">₦ <span id="grand-total-val">0.00</span></h3>
                        </div>

                        <div class="mb-3">
                            <label class="side-label">Global Funding Source</label>
                            <input type="text" name="funding_source" class="form-control form-control-sm bg-transparent text-white border-secondary" placeholder="e.g. College Budget 2026">
                        </div>

                        <div class="mb-3">
                            <label class="side-label">Overall Notes</label>
                            <textarea name="notes" class="form-control form-control-sm bg-transparent text-white border-secondary" rows="2" placeholder="General context..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="side-label">Executive Summary</label>
                            <textarea name="summary" class="form-control form-control-sm bg-transparent text-white border-secondary" rows="2" placeholder="Brief summary for approval..."></textarea>
                        </div>

                        <div class="mb-4 d-none d-md-block">
                            <h6 class="side-label">Originating Entity</h6>
                            @php $u = auth()->user(); @endphp
                            @foreach(['faculty', 'institute', 'department', 'office', 'unit'] as $entity)
                                @if($u && $u->$entity)
                                    <div class="entity-tag">
                                        <span class="opacity-75" style="font-size: 0.6rem;">{{ strtoupper($entity) }}</span><br>
                                        <b>{{ $u->$entity->{$entity.'_name'} }}</b>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow">
                            <i class="fas fa-check-circle me-2"></i> Finalize Submission
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    /* Logic preserved exactly as original */
    const subMap = {!! json_encode($subcategoryMap) !!};
    let itemIndex = 1; 
    let rowFilesTracker = { 0: [] }; 

    function handleFileSelect(input) {
        const row = input.closest('.item-row');
        const idx = row.getAttribute('data-row-index');
        if (!rowFilesTracker[idx]) rowFilesTracker[idx] = [];
        rowFilesTracker[idx] = [...rowFilesTracker[idx], ...Array.from(input.files)];
        syncRowFiles(idx);
        input.value = ''; 
    }

    function syncRowFiles(idx) {
        const row = document.querySelector(`.item-row[data-row-index="${idx}"]`);
        const previewZone = row.querySelector('.file-preview-container');
        const hiddenZone = row.querySelector('.hidden-inputs-container');
        const itemName = row.querySelector('input[name*="[item_name]"]').value || "Asset";
        
        previewZone.innerHTML = ''; hiddenZone.innerHTML = '';
        const dt = new DataTransfer();
        
        rowFilesTracker[idx].forEach((file, fileIdx) => {
            dt.items.add(file);
            const div = document.createElement('div');
            div.className = 'preview-item';
            let icon = file.type.startsWith('image/') 
                ? `<img src="${URL.createObjectURL(file)}" alt="Preview">` 
                : `<div class="py-2"><i class="fas fa-file-pdf fa-2x text-danger"></i></div>`;

            div.innerHTML = `${icon}<div class="small text-truncate p-1">${itemName}_${fileIdx+1}</div>
                             <span class="remove-file" onclick="removeRowFile(${idx}, ${fileIdx})">×</span>`;
            previewZone.appendChild(div);
        });
        
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'file'; 
        hiddenInput.name = `items[${idx}][documents][]`;
        hiddenInput.multiple = true; 
        hiddenInput.files = dt.files;
        hiddenZone.appendChild(hiddenInput);
    }

    window.removeRowFile = (rowIdx, fileIdx) => {
        rowFilesTracker[rowIdx].splice(fileIdx, 1);
        syncRowFiles(rowIdx);
    };

    function updateSubcats(sel) {
        const row = sel.closest('.item-row');
        const subSel = row.querySelector('.subcategory-select');
        subSel.innerHTML = '<option value="">-- Select --</option>';
        if (sel.value && subMap[sel.value]) {
            Object.values(subMap[sel.value]).forEach(s => {
                subSel.add(new Option(s.subcategory_name, s.subcategory_id));
            });
        }
    }

    function toggleRowFields(sel) {
        const row = sel.closest('.item-row');
        const isNew = sel.value === 'new_purchase';
        row.querySelector('.cost-area').style.visibility = isNew ? 'visible' : 'hidden';
        row.querySelector('.serial-area').style.visibility = isNew ? 'visible' : 'hidden';
        runGrandTotal();
    }

    function runGrandTotal() {
        let grand = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const q = parseFloat(row.querySelector('.qty-input').value) || 0;
            const c = parseFloat(row.querySelector('.cost-input').value) || 0;
            grand += (q * c);
        });
        document.getElementById('grand-total-val').textContent = grand.toLocaleString('en-US', {minimumFractionDigits: 2});
    }

    document.getElementById('add-row').addEventListener('click', () => {
        const container = document.getElementById('items-container');
        const firstRow = document.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);
        const idx = itemIndex++;

        newRow.setAttribute('data-row-index', idx);
        rowFilesTracker[idx] = []; 
        
        newRow.querySelectorAll('input, select, textarea').forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/items\[\d+\]/, `items[${idx}]`);
            }
            if(input.type !== 'file' && input.type !== 'hidden') {
                input.value = input.classList.contains('qty-input') ? 1 : '';
            }
        });

        newRow.querySelector('.subcategory-select').innerHTML = '<option value="">-- Select Category First --</option>';
        newRow.querySelector('.file-preview-container').innerHTML = '';
        newRow.querySelector('.hidden-inputs-container').innerHTML = '';

        const delBtn = document.createElement('button');
        delBtn.type = 'button';
        delBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle';
        delBtn.innerHTML = '<i class="fas fa-times"></i>';
        delBtn.onclick = function() {
            newRow.remove();
            delete rowFilesTracker[idx];
            renumberAll();
            runGrandTotal();
        };
        newRow.appendChild(delBtn);

        container.appendChild(newRow);
        renumberAll();
        runGrandTotal();
    });

    function renumberAll() {
        document.querySelectorAll('.item-row').forEach((row, i) => {
            row.querySelector('.item-number-label').innerHTML = `<i class="fas fa-box me-2"></i>Asset Entry #${i + 1}`;
        });
    }
</script>
@endsection