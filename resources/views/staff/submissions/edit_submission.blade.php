@extends('layouts.staff')

@section('title', 'Edit Inventory Audit')

@section('content')
<style>
    :root {
        --med-navy: #0f172a;
        --med-blue: #3b82f6;
        --med-slate: #f1f5f9;
        --med-border: #e2e8f0;
        --med-accent: #7dd3fc;
    }

    /* Professional Reduced Text Size */
    body { background-color: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; font-size: 0.82rem; }
    
    .glass-header { 
        background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);
        border-bottom: 1px solid var(--med-border); position: sticky; top: 0; z-index: 1000; padding: 0.8rem 0;
    }

    .mode-badge { font-size: 0.7rem; font-weight: 800; padding: 5px 14px; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.5px; }
    .mode-single { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .mode-bulk { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }

    .item-row { 
        background: #fff; border: 1px solid var(--med-border); border-radius: 20px; 
        margin-bottom: 2rem; position: relative; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }
    
    .entry-header {
        border-bottom: 1px solid var(--med-border); padding: 15px 25px;
        background: #fafafa; border-radius: 20px 20px 0 0;
    }

    .form-control, .form-select { 
        border: 1.5px solid var(--med-border); border-radius: 10px; padding: 0.5rem 1rem; 
        font-size: 0.85rem; /* Slightly larger than body for legibility in inputs */
    }

    /* FIXED: Arrow overlaying text */
    .form-select {
        padding-right: 2.5rem !important;
        background-position: right 0.75rem center;
    }

    .item-admin-context { 
        background: #f8fafc; border-radius: 14px; padding: 18px; 
        border: 1px solid var(--med-border); margin-bottom: 20px; 
    }

    .sidebar-meta { background: var(--med-navy); color: white; border-radius: 24px; padding: 22px; }
    .side-label { font-size: 0.65rem; font-weight: 800; color: var(--med-accent); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; }
</style>

<div class="glass-header mb-4">
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1" style="font-size: 0.75rem;">
                        <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('staff.submissions.index') }}">Submissions</a></li>
                        <li class="breadcrumb-item active">Edit #{{ $submission->submission_id }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <h2 class="h5 fw-bold mb-0">Modify Submission</h2>
                    <span id="submission-mode-badge" class="mode-badge mode-single">Single Entry</span>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('staff.submissions.show', $submission->submission_id) }}" class="btn btn-light border rounded-pill px-4 fw-bold">Discard</a>
                <button type="submit" form="audit-form" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">Update Record</button>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid px-4">
    <form action="{{ route('staff.submissions.update', $submission->submission_id) }}" method="POST" enctype="multipart/form-data" id="audit-form">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-lg-9">
                <div id="items-container">
                    @foreach($submission->items as $index => $item)
                    <div class="item-row" data-row-index="{{ $index }}">
                        <div class="entry-header d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold text-dark mb-0 small text-uppercase">Record Entry <span class="entry-number">#{{ $index + 1 }}</span></h6>
                            <button type="button" class="btn btn-link text-danger text-decoration-none fw-bold small" onclick="removeItemRow(this)">
                                <i class="fas fa-trash-alt me-1"></i> Remove Item
                            </button>
                        </div>

                        <div class="p-4">
                            <div class="row g-4">
                                <div class="col-md-7">
                                    <div class="item-admin-context">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Entry Type</label>
                                                <select name="items[{{ $index }}][submission_type]" class="form-select" onchange="toggleEntryTypeForRow(this)">
                                                    <option value="new_purchase" {{ $item->submission_type == 'new_purchase' ? 'selected' : '' }}>New Purchase</option>
                                                    <option value="transfer" {{ $item->submission_type == 'transfer' ? 'selected' : '' }}>Internal Transfer</option>
                                                    <option value="disposal" {{ $item->submission_type == 'disposal' ? 'selected' : '' }}>Disposal</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small">Funding Source</label>
                                                <input type="text" name="items[{{ $index }}][funding_source_per_item]" value="{{ $item->funding_source_per_item }}" class="form-control" placeholder="e.g. TETFund">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">Category</label>
                                            <select name="items[{{ $index }}][category_id]" class="form-select category-select" onchange="updateSubcategories(this)">
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->category_id }}" {{ $item->category_id == $cat->category_id ? 'selected' : '' }}>{{ $cat->category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold small">Sub-Category</label>
                                            <select name="items[{{ $index }}][subcategory_id]" class="form-select subcategory-select" data-selected="{{ $item->subcategory_id }}">
                                                <option value="">-- Choose Category --</option>
                                            </select>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-bold small">Item Description</label>
                                            <input type="text" name="items[{{ $index }}][item_name]" class="form-control" value="{{ $item->item_name }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold small">Quantity</label>
                                            <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" class="form-control fw-bold">
                                        </div>
                                        <div class="col-md-4 cost-field">
                                            <label class="form-label fw-bold small">Unit Cost (₦)</label>
                                            <input type="number" step="0.01" name="items[{{ $index }}][cost]" class="form-control" value="{{ $item->cost }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold small">Serial/Asset No.</label>
                                            <input type="text" name="items[{{ $index }}][serial_number]" class="form-control" value="{{ $item->serial_number }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5 border-start">
                                    <div class="mb-4">
                                        <label class="form-label fw-bold small">Evidence & Documents</label>
                                        <div class="file-preview-container d-flex flex-wrap gap-2 mb-3">
                                            @php $files = is_string($item->document_path) ? json_decode($item->document_path, true) : $item->document_path; @endphp
                                            @foreach($files ?? [] as $fIdx => $path)
                                                <div class="preview-item border rounded p-1 position-relative" id="item-{{ $index }}-file-{{ $fIdx }}" style="width: 80px;">
                                                    <div class="delete-cross" style="position:absolute; top:-5px; right:-5px; background:red; color:white; border-radius:50%; width:18px; height:18px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:10px;" onclick="removeExistingItemFile({{ $index }}, {{ $fIdx }})">&times;</div>
                                                    <input type="hidden" name="items[{{ $index }}][existing_files][]" value="{{ $path }}" id="item-{{ $index }}-input-{{ $fIdx }}">
                                                    <a href="{{ Storage::url($path) }}" target="_blank" class="text-decoration-none text-center d-block">
                                                        @php $ext = pathinfo($path, PATHINFO_EXTENSION); @endphp
                                                        @if(in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'webp']))
                                                            <img src="{{ Storage::url($path) }}" style="width:100%; height:50px; object-fit:cover;">
                                                        @else
                                                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                        @endif
                                                        <div class="small text-muted text-truncate mt-1" style="font-size: 0.6rem;">{{ $item->item_name }}_{{ $fIdx+1 }}</div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="p-3 border rounded bg-light">
                                            <span class="existing-label text-muted d-block mb-2" style="font-size: 0.65rem; font-weight: 800; text-transform: uppercase;">Upload New</span>
                                            <input type="file" class="form-control form-control-sm" multiple onchange="handleFileSelect(this)">
                                            <div class="hidden-inputs-container" style="display: none;"></div>
                                            <div class="file-preview-container mt-2 d-flex flex-wrap gap-2"></div>
                                        </div>
                                    </div>
                                    <label class="form-label fw-bold small">Individual Asset Notes</label>
                                    <textarea name="items[{{ $index }}][item_notes]" rows="3" class="form-control small">{{ $item->item_notes }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" id="add-item" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm mb-5">
                    <i class="fas fa-plus-circle me-2 text-primary"></i> Add Another Item
                </button>
            </div>

            <div class="col-lg-3">
                <div class="sticky-top" style="top: 100px;">
                    <div class="sidebar-meta shadow-sm">
                        <h6 class="fw-bold mb-4 text-white">Review Summary</h6>
                        <div class="mb-4">
                            <div class="side-label">Originating Entity</div>
                            <div class="p-3 rounded-3" style="background: rgba(255,255,255,0.05); border-left: 4px solid var(--med-accent);">
                                @php $u = auth()->user(); @endphp
                                @if($u->faculty) <div class="fw-bold small text-white">{{ $u->faculty->faculty_name }}</div> @endif
                                @if($u->department) <div class="side-text" style="font-size: 0.7rem;">{{ $u->department->dept_name }}</div> @endif
                                @if($u->office) <div class="fw-bold small text-white">{{ $u->office->office_name }}</div> @endif
                                @if($u->unit) <div class="side-text" style="font-size: 0.7rem;">{{ $u->unit->unit_name }}</div> @endif
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="side-label">Overall Notes</label>
                            <textarea name="notes" class="form-control form-control-sm bg-transparent text-white border-secondary" rows="3">{{ $submission->notes }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="side-label">Executive Summary</label>
                            <textarea name="summary" class="form-control form-control-sm bg-transparent text-white border-secondary" rows="3">{{ $submission->summary }}</textarea>
                        </div>
                        <div class="mt-4 pt-3 border-top border-secondary d-flex justify-content-between">
                            <span class="small opacity-75">Record Count:</span>
                            <span id="summary-count" class="fw-bold text-white">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const subcategoryMap = {!! json_encode($subcategoryMap, JSON_FORCE_OBJECT) !!};
    let itemCount = {{ $submission->items->count() }};
    let itemFiles = {};

    function updateSubmissionMode() {
        const rows = document.querySelectorAll('.item-row');
        const count = rows.length;
        document.getElementById('summary-count').innerText = count;
        const badge = document.getElementById('submission-mode-badge');
        badge.innerText = count > 1 ? "Bulk Submission" : "Single Entry";
        badge.className = count > 1 ? "mode-badge mode-bulk" : "mode-badge mode-single";
        rows.forEach((el, i) => { el.querySelector('.entry-number').innerText = '#' + (i + 1); });
    }

    function removeExistingItemFile(itemIdx, fileIdx) {
        if(confirm('Permanently remove this file?')) {
            document.getElementById(`item-${itemIdx}-input-${fileIdx}`).remove();
            document.getElementById(`item-${itemIdx}-file-${fileIdx}`).remove();
        }
    }

    function updateSubcategories(selectElement, isInitial = false) {
        const row = selectElement.closest('.item-row');
        const subSelect = row.querySelector('.subcategory-select');
        const catId = selectElement.value;
        const preSelected = subSelect.getAttribute('data-selected');
        subSelect.innerHTML = '<option value="">-- Choose Category --</option>';
        if (catId && subcategoryMap[catId]) {
            Object.values(subcategoryMap[catId]).forEach(sub => {
                const opt = new Option(sub.subcategory_name, sub.subcategory_id);
                if(isInitial && sub.subcategory_id == preSelected) opt.selected = true;
                subSelect.add(opt);
            });
        }
    }

    function handleFileSelect(input) {
        const row = input.closest('.item-row');
        const idx = row.getAttribute('data-row-index');
        if (!itemFiles[idx]) itemFiles[idx] = [];
        itemFiles[idx] = [...itemFiles[idx], ...Array.from(input.files)];
        syncRowFiles(idx);
        input.value = ''; 
    }

    function syncRowFiles(idx) {
        const row = document.querySelector(`.item-row[data-row-index="${idx}"]`);
        const previewZone = row.querySelector('.p-3 .file-preview-container');
        const hiddenZone = row.querySelector('.hidden-inputs-container');
        const itemName = row.querySelector('input[name*="[item_name]"]').value || "New_Item";
        
        previewZone.innerHTML = ''; hiddenZone.innerHTML = '';
        const dt = new DataTransfer();
        itemFiles[idx].forEach((f, fIdx) => {
            dt.items.add(f);
            const div = document.createElement('div');
            div.className = 'preview-item border rounded p-1 position-relative';
            div.style.width = '80px';
            div.innerHTML = `<div class="delete-cross" style="position:absolute; top:-5px; right:-5px; background:red; color:white; border-radius:50%; width:18px; height:18px; display:flex; align-items:center; justify-content:center; cursor:pointer; font-size:10px;" onclick="removeRowFile(${idx}, ${fIdx})">&times;</div>
                ${f.type.startsWith('image/') ? `<img src="${URL.createObjectURL(f)}" style="width:100%; height:50px; object-fit:cover;">` : `<i class="fas fa-file-pdf fa-2x text-danger d-block text-center py-2"></i>`}
                <div class="text-muted text-truncate mt-1 text-center" style="font-size: 0.6rem;">${itemName}</div>`;
            previewZone.appendChild(div);
        });
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'file'; hiddenInput.name = `items[${idx}][new_evidence][]`;
        hiddenInput.multiple = true; hiddenInput.files = dt.files;
        hiddenZone.appendChild(hiddenInput);
    }

    function removeRowFile(rowIdx, fIdx) { itemFiles[rowIdx].splice(fIdx, 1); syncRowFiles(rowIdx); }

    document.getElementById('add-item').addEventListener('click', () => {
        const container = document.getElementById('items-container');
        const firstRow = document.querySelector('.item-row');
        const newRow = firstRow.cloneNode(true);
        newRow.setAttribute('data-row-index', itemCount);
        newRow.querySelectorAll('input, select, textarea').forEach(el => {
            if(el.name) el.name = el.name.replace(/items\[\d+\]/, `items[${itemCount}]`);
            if(el.type !== 'hidden') el.value = '';
        });
        newRow.querySelectorAll('.file-preview-container').forEach(c => c.innerHTML = '');
        container.appendChild(newRow);
        itemCount++; updateSubmissionMode();
    });

    function removeItemRow(btn) {
        if(document.querySelectorAll('.item-row').length > 1) {
            btn.closest('.item-row').remove(); updateSubmissionMode();
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.category-select').forEach(s => updateSubcategories(s, true));
        updateSubmissionMode();
    });
</script>
@endsection