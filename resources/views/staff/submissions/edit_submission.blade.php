@extends('layouts.staff')

@section('title', 'Edit Submission')

@section('content')

<div class="container-fluid px-3 px-lg-5 py-4" style="max-width: 1600px;">
    
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('staff.submissions.show', $submission->submission_id) }}" 
               class="btn btn-white border border-slate-200 rounded-circle shadow-sm d-flex align-items-center justify-content-center"
               style="width:44px; height:44px; transition:all 0.2s;"
               onmouseenter="this.style.background='#0f172a'; this.style.borderColor='#0f172a'; this.querySelector('i').style.color='#fff';"
               onmouseleave="this.style.background='#fff'; this.style.borderColor='#e2e8f0'; this.querySelector('i').style.color='#94a3b8';">
                <i class="fas fa-arrow-left text-slate-400" style="font-size:0.85rem; transition:color 0.2s;"></i>
            </a>
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size:0.7rem;">
                        <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}" class="text-decoration-none text-slate-500 fw-bold">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('staff.submissions.index') }}" class="text-decoration-none text-slate-500 fw-bold">Submissions</a></li>
                        <li class="breadcrumb-item active text-emerald-600 fw-bold">Edit #{{ str_pad($submission->submission_id, 4, '0', STR_PAD_LEFT) }}</li>
                    </ol>
                </nav>
                <div class="d-flex align-items-center gap-2">
                    <h1 class="fw-black text-slate-900 mb-0" style="font-size:1.5rem; letter-spacing:-0.02em;">Edit Submission</h1>
                    <span id="mode-badge" class="badge rounded-pill fw-bold" 
                          style="font-size:0.7rem; padding:0.4rem 0.9rem; background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0;">
                        Single Entry
                    </span>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('staff.submissions.show', $submission->submission_id) }}" 
               class="btn btn-white border border-slate-200 rounded-3 px-4 py-2 fw-bold"
               style="font-size:0.82rem;">
                <i class="fas fa-times me-1 text-slate-600"></i> Cancel
            </a>
            <button type="submit" form="edit-form" 
                    class="btn text-white fw-black rounded-3 px-4 py-2 shadow-lg"
                    style="background:linear-gradient(135deg, #059669, #047857); font-size:0.82rem;">
                <i class="fas fa-save me-1"></i> Save Changes
            </button>
        </div>
    </div>

    <form action="{{ route('staff.submissions.update', $submission->submission_id) }}" 
          method="POST" 
          enctype="multipart/form-data" 
          id="edit-form">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            
            {{-- Left Column: Items --}}
            <div class="col-lg-8">
                <div id="items-container">
                    @foreach($submission->items as $index => $item)
                    <div class="item-row bg-white rounded-4 border border-slate-200 shadow-sm mb-4 overflow-hidden" 
                         data-row-index="{{ $index }}"
                         style="animation:fadeInUp 0.3s ease-out both; animation-delay:{{ $index * 0.05 }}s;">
                        
                        {{-- Item Header --}}
                        <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                             style="background:linear-gradient(to right, #f8fafc, #f1f5f9);">
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle bg-emerald-600 d-flex align-items-center justify-content-center text-white fw-black"
                                     style="width:32px; height:32px; font-size:0.85rem;">
                                    <span class="entry-number">{{ $index + 1 }}</span>
                                </div>
                                <span class="fw-black text-slate-900" style="font-size:0.9rem;">Asset Entry</span>
                            </div>
                            
                            @if($submission->items->count() > 1)
                            <button type="button" 
                                    class="btn btn-sm btn-white border border-slate-200 rounded-2 fw-bold d-flex align-items-center gap-1"
                                    style="font-size:0.75rem;"
                                    onclick="removeItemRow(this)">
                                <i class="fas fa-trash-alt text-danger"></i>
                                Remove
                            </button>
                            @endif
                        </div>

                        <div class="p-4">
                            {{-- Hidden field to track which item this is (for updates) --}}
                            <input type="hidden" 
                                   name="items[{{ $index }}][submission_item_id]" 
                                   value="{{ $item->submission_item_id }}">
                            
                            {{-- Entry Type Section --}}
                            <div class="rounded-3 p-3 mb-4" style="background:#f0fdf4; border:1px solid #d1fae5;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-slate-700" style="font-size:0.75rem;">Entry Type</label>
                                        <select name="items[{{ $index }}][submission_type]" 
                                                class="form-select border-slate-200 shadow-sm" 
                                                style="font-size:0.85rem; border-radius:10px;"
                                                onchange="toggleRowFields(this)">
                                            <option value="new_purchase" {{ $item->submission_type == 'new_purchase' ? 'selected' : '' }}>New Purchase</option>
                                            <option value="transfer" {{ $item->submission_type == 'transfer' ? 'selected' : '' }}>Internal Transfer</option>
                                            <option value="disposal" {{ $item->submission_type == 'disposal' ? 'selected' : '' }}>Disposal</option>
                                            <option value="audit" {{ $item->submission_type == 'audit' ? 'selected' : '' }}>Existing Audit/Update</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-emerald-700" style="font-size:0.75rem;">Item Notes</label>
                                        <textarea name="items[{{ $index }}][item_notes]" rows="2" 
                                                  class="form-control border-slate-200 shadow-sm" 
                                                  style="font-size:0.82rem; border-radius:10px;"
                                                  placeholder="Condition or special details...">{{ $item->item_notes }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Main Asset Details --}}
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700" style="font-size:0.75rem;">
                                        <i class="fas fa-tags text-slate-400 me-1" style="font-size:0.7rem;"></i>
                                        Category
                                    </label>
                                    <select name="items[{{ $index }}][category_id]" 
                                            class="form-select category-select border-slate-200 shadow-sm" 
                                            style="font-size:0.85rem; border-radius:10px;"
                                            onchange="updateSubcats(this)" 
                                            required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->category_id }}" {{ $item->category_id == $cat->category_id ? 'selected' : '' }}>
                                                {{ $cat->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-slate-700" style="font-size:0.75rem;">
                                        <i class="fas fa-layer-group text-slate-400 me-1" style="font-size:0.7rem;"></i>
                                        Sub-Category
                                    </label>
                                    <select name="items[{{ $index }}][subcategory_id]" 
                                            class="form-select subcategory-select border-slate-200 shadow-sm" 
                                            style="font-size:0.85rem; border-radius:10px;"
                                            data-selected="{{ $item->subcategory_id }}"
                                            required>
                                        <option value="">-- Select Category First --</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-bold text-slate-700" style="font-size:0.75rem;">
                                        <i class="fas fa-box text-slate-400 me-1" style="font-size:0.7rem;"></i>
                                        Item Name / Model Description
                                    </label>
                                    <input type="text" 
                                           name="items[{{ $index }}][item_name]" 
                                           class="form-control border-slate-200 shadow-sm" 
                                           style="font-size:0.85rem; border-radius:10px;"
                                           value="{{ $item->item_name }}"
                                           required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-slate-700" style="font-size:0.75rem;">
                                        <i class="fas fa-hashtag text-slate-400 me-1" style="font-size:0.7rem;"></i>
                                        Quantity
                                    </label>
                                    <input type="number" 
                                           name="items[{{ $index }}][quantity]" 
                                           value="{{ $item->quantity }}" 
                                           min="1" 
                                           class="form-control qty-input border-slate-200 shadow-sm" 
                                           style="font-size:0.85rem; border-radius:10px;">
                                </div>

                                <div class="col-md-4 cost-area">
                                    <label class="form-label fw-bold text-slate-700" style="font-size:0.75rem;">
                                        <i class="fas fa-naira-sign text-slate-400 me-1" style="font-size:0.7rem;"></i>
                                        Unit Cost (₦)
                                    </label>
                                    <input type="number" 
                                           step="0.01" 
                                           name="items[{{ $index }}][cost]" 
                                           class="form-control cost-input border-slate-200 shadow-sm" 
                                           style="font-size:0.85rem; border-radius:10px;"
                                           value="{{ $item->cost }}"
                                           placeholder="0.00">
                                </div>

                                <div class="col-md-4 serial-area">
                                    <label class="form-label fw-bold text-slate-700" style="font-size:0.75rem;">
                                        <i class="fas fa-barcode text-slate-400 me-1" style="font-size:0.7rem;"></i>
                                        Serial Number
                                    </label>
                                    <input type="text" 
                                           name="items[{{ $index }}][serial_number]" 
                                           class="form-control border-slate-200 shadow-sm" 
                                           style="font-size:0.85rem; border-radius:10px;"
                                           value="{{ $item->serial_number }}"
                                           placeholder="Optional">
                                </div>

                                {{-- File Management Section --}}
                                <div class="col-12 mt-4">
                                    <label class="form-label fw-bold text-slate-700 mb-3" style="font-size:0.75rem;">
                                        <i class="fas fa-paperclip text-slate-400 me-1" style="font-size:0.7rem;"></i>
                                        Supporting Evidence
                                    </label>
                                    
                                    {{-- Existing Files --}}
                                    @php 
                                        $files = is_string($item->document_path) ? json_decode($item->document_path, true) : $item->document_path; 
                                    @endphp
                                    
                                    @if(!empty($files))
                                    <div class="mb-3">
                                        <div class="text-slate-600 fw-bold mb-2" style="font-size:0.72rem;">Current Files:</div>
                                        <div class="d-flex flex-wrap gap-2" id="existing-files-{{ $index }}">
                                            @foreach($item->documents as $fIdx => $file)
    <div class="position-relative preview-item..." id="existing-file-{{ $index }}-{{ $fIdx }}">
        
        <input type="hidden" 
               name="items[{{ $index }}][existing_files][]" 
               value="{{ $file->path }}" 
               id="existing-input-{{ $index }}-{{ $fIdx }}">

        <a href="{{ Storage::url($file->path) }}" target="_blank">
            @if($file->is_image)
                 <img src="{{ Storage::url($file->path) }}" class="w-100 rounded-2 mb-1" style="height:60px; object-fit:cover;">
            @else
                 <div class="text-center py-2">
                     <i class="fas fa-file-pdf text-danger" style="font-size:2rem;"></i>
                 </div>
            @endif
            <div class="text-center text-slate-600 text-truncate" style="font-size:0.65rem;">
                {{ $file->name }}
            </div>
        </a>
    </div>
@endforeach
                                        </div>
                                    </div>
                                    @endif

                                    {{-- New Files Upload --}}
                                    <div class="rounded-3 p-4 border-2 border-dashed border-slate-300 bg-slate-50 text-center"
                                         style="transition:all 0.2s;"
                                         onmouseenter="this.style.borderColor='#059669'; this.style.background='#f0fdf4';"
                                         onmouseleave="this.style.borderColor='#cbd5e1'; this.style.background='#f8fafc';">
                                        <input type="file" 
                                               class="form-control d-none new-file-input" 
                                               multiple 
                                               accept="image/*,.pdf"
                                               onchange="handleNewFiles(this)">
                                        <i class="fas fa-cloud-upload-alt text-slate-400 mb-2" style="font-size:2rem;"></i>
                                        <p class="text-slate-600 mb-1 fw-bold" style="font-size:0.85rem;">Add New Files</p>
                                        <p class="text-slate-500 mb-2" style="font-size:0.72rem;">JPG, PNG, PDF • Max 10MB</p>
                                        <button type="button" 
                                                class="btn btn-sm btn-emerald-600 text-white fw-bold rounded-2"
                                                style="font-size:0.75rem; padding:0.4rem 1rem;"
                                                onclick="this.previousElementSibling.previousElementSibling.previousElementSibling.click()">
                                            <i class="fas fa-plus me-1"></i> Browse Files
                                        </button>
                                        <div class="hidden-inputs-container d-none"></div>
                                        <div class="new-files-preview mt-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Add Another Item Button --}}
                @if($submission->status == 'pending')
                <button type="button" 
                        id="add-item" 
                        class="btn btn-white border border-slate-200 rounded-3 px-4 py-3 fw-bold shadow-sm w-100 mb-5"
                        style="font-size:0.85rem; transition:all 0.2s;"
                        onmouseenter="this.style.background='#059669'; this.style.borderColor='#059669'; this.style.color='#fff';"
                        onmouseleave="this.style.background='#fff'; this.style.borderColor='#e2e8f0'; this.style.color='#475569';">
                    <i class="fas fa-plus-circle me-2"></i> Add Another Asset Entry
                </button>
                @endif
            </div>

            {{-- Right Column: Summary --}}
            <div class="col-lg-4">
                <div class="sticky-top" style="top:85px;">
                    
                    {{-- Item Count Card --}}
                    <div class="rounded-4 overflow-hidden shadow-lg mb-4"
                         style="background:linear-gradient(135deg, #059669 0%, #047857 100%);">
                        <div class="p-4 text-center">
                            <p class="text-emerald-100 text-uppercase mb-2 fw-black" style="font-size:0.7rem; letter-spacing:0.1em;">
                                Total Items
                            </p>
                            <h2 class="text-white fw-black mb-0" style="font-size:2.5rem; letter-spacing:-0.02em;" id="item-count">
                                {{ $submission->items->count() }}
                            </h2>
                            <p class="text-emerald-200 mb-0 mt-2" style="font-size:0.72rem;">
                                Asset Entries
                            </p>
                        </div>
                    </div>

                    {{-- Submission Details --}}
                    <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden mb-4">
                        <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                             style="background:linear-gradient(to right, #f8fafc, #f1f5f9);">
                            <h6 class="fw-black text-slate-900 mb-0" style="font-size:0.9rem;">Submission Details</h6>
                        </div>
                        <div class="p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-slate-700" style="font-size:0.75rem;">
                                    <i class="fas fa-sticky-note text-slate-400 me-1" style="font-size:0.7rem;"></i>
                                    Overall Notes
                                </label>
                                <textarea name="notes" rows="3" 
                                          class="form-control border-slate-200 shadow-sm" 
                                          style="font-size:0.82rem; border-radius:10px;"
                                          placeholder="General context...">{{ $submission->notes }}</textarea>
                            </div>

                            <div class="mb-0">
                                <label class="form-label fw-bold text-slate-700" style="font-size:0.75rem;">
                                    <i class="fas fa-file-alt text-slate-400 me-1" style="font-size:0.7rem;"></i>
                                    Executive Summary
                                </label>
                                <textarea name="summary" rows="3" 
                                          class="form-control border-slate-200 shadow-sm" 
                                          style="font-size:0.82rem; border-radius:10px;"
                                          placeholder="Brief summary...">{{ $submission->summary }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Originating Entity --}}
                    <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                             style="background:linear-gradient(to right, #f8fafc, #f1f5f9);">
                            <h6 class="fw-black text-slate-900 mb-0" style="font-size:0.9rem;">Submitting Entity</h6>
                        </div>
                        <div class="p-4">
                            @php $u = auth()->user(); @endphp
                            @foreach(['faculty', 'institute', 'department', 'office', 'unit'] as $entity)
                                @if($u && $u->$entity)
                                    <div class="d-flex align-items-start gap-3 mb-3 pb-3 {{ $loop->last ? '' : 'border-bottom border-slate-100' }}">
                                        <div class="rounded-3 bg-emerald-50 d-flex align-items-center justify-content-center flex-shrink-0"
                                             style="width:44px; height:44px;">
                                            <i class="fas fa-{{ $entity == 'faculty' ? 'graduation-cap' : ($entity == 'institute' ? 'university' : ($entity == 'department' ? 'building-columns' : ($entity == 'office' ? 'briefcase' : 'microscope'))) }} text-emerald-600" style="font-size:1.1rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <p class="text-slate-500 text-uppercase mb-1 fw-bold" style="font-size:0.65rem; letter-spacing:0.08em;">{{ $entity }}</p>
                                            <p class="text-slate-900 fw-bold mb-0" style="font-size:0.88rem;">{{ $u->$entity->{$entity.'_name'} }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
@keyframes fadeInUp {
    from { opacity:0; transform:translateY(10px); }
    to { opacity:1; transform:translateY(0); }
}

.btn-emerald-600 {
    background: #059669;
}

.btn-emerald-600:hover {
    background: #047857;
}

.preview-item {
    transition: all 0.2s;
}

.preview-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>

<script>
const subMap = {!! json_encode($subcategoryMap) !!};
let itemIndex = {{ $submission->items->count() }};
let newFilesTracker = {};

function updateModeBadge() {
    const count = document.querySelectorAll('.item-row').length;
    document.getElementById('item-count').textContent = count;
    
    const badge = document.getElementById('mode-badge');
    if (count > 1) {
        badge.textContent = 'Bulk Entry';
        badge.style.background = '#eff6ff';
        badge.style.color = '#1e40af';
        badge.style.borderColor = '#bfdbfe';
    } else {
        badge.textContent = 'Single Entry';
        badge.style.background = '#ecfdf5';
        badge.style.color = '#065f46';
        badge.style.borderColor = '#a7f3d0';
    }
    
    // Renumber entries
    document.querySelectorAll('.entry-number').forEach((el, i) => {
        el.textContent = i + 1;
    });
}

function removeExistingFile(itemIdx, fileIdx) {
    if (confirm('Remove this file? This action cannot be undone.')) {
        document.getElementById(`existing-file-${itemIdx}-${fileIdx}`).remove();
        document.getElementById(`existing-input-${itemIdx}-${fileIdx}`).remove();
    }
}

function updateSubcats(sel, isInitial = false) {
    const row = sel.closest('.item-row');
    const subSel = row.querySelector('.subcategory-select');
    const catId = sel.value;
    const preSelected = subSel.getAttribute('data-selected');
    
    subSel.innerHTML = '<option value="">-- Select --</option>';
    if (catId && subMap[catId]) {
        Object.values(subMap[catId]).forEach(sub => {
            const opt = new Option(sub.subcategory_name, sub.subcategory_id);
            if (isInitial && sub.subcategory_id == preSelected) {
                opt.selected = true;
            }
            subSel.add(opt);
        });
    }
}

function toggleRowFields(sel) {
    const row = sel.closest('.item-row');
    const isNew = sel.value === 'new_purchase';
    row.querySelector('.cost-area').style.display = isNew ? 'block' : 'none';
    row.querySelector('.serial-area').style.display = isNew ? 'block' : 'none';
}

function handleNewFiles(input) {
    const row = input.closest('.item-row');
    const idx = row.getAttribute('data-row-index');
    const previewZone = row.querySelector('.new-files-preview');
    const hiddenZone = row.querySelector('.hidden-inputs-container');
    
    if (!newFilesTracker[idx]) newFilesTracker[idx] = [];
    newFilesTracker[idx] = [...newFilesTracker[idx], ...Array.from(input.files)];
    
    previewZone.innerHTML = '';
    hiddenZone.innerHTML = '';
    const dt = new DataTransfer();
    
    newFilesTracker[idx].forEach((file, fIdx) => {
        dt.items.add(file);
        const div = document.createElement('div');
        div.className = 'preview-item border border-slate-200 rounded-3 p-2 bg-white position-relative d-inline-block me-2 mb-2';
        div.style.width = '100px';
        
        let content;
        if (file.type.startsWith('image/')) {
            content = `<img src="${URL.createObjectURL(file)}" class="w-100 rounded-2" style="height:60px; object-fit:cover;">`;
        } else {
            content = `<div class="text-center py-2"><i class="fas fa-file-pdf text-danger" style="font-size:2rem;"></i></div>`;
        }
        
        div.innerHTML = `
            <button type="button" class="position-absolute btn btn-sm btn-danger rounded-circle" 
                    style="top:-8px; right:-8px; width:24px; height:24px; padding:0; font-size:0.7rem;"
                    onclick="removeNewFile(${idx}, ${fIdx})">
                <i class="fas fa-times"></i>
            </button>
            ${content}
            <div class="text-center text-slate-600 text-truncate mt-1" style="font-size:0.65rem;">New ${fIdx + 1}</div>
        `;
        previewZone.appendChild(div);
    });
    
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'file';
    hiddenInput.name = `items[${idx}][new_evidence][]`;
    hiddenInput.multiple = true;
    hiddenInput.files = dt.files;
    hiddenZone.appendChild(hiddenInput);
    
    input.value = '';
}

window.removeNewFile = function(rowIdx, fileIdx) {
    newFilesTracker[rowIdx].splice(fileIdx, 1);
    const row = document.querySelector(`.item-row[data-row-index="${rowIdx}"]`);
    const input = row.querySelector('.new-file-input');
    const fakeEvent = { target: { files: newFilesTracker[rowIdx] } };
    // Recreate preview
    const dt = new DataTransfer();
    newFilesTracker[rowIdx].forEach(f => dt.items.add(f));
    input.files = dt.files;
    handleNewFiles(input);
};

function removeItemRow(btn) {
    if (document.querySelectorAll('.item-row').length > 1) {
        if (confirm('Remove this item entry?')) {
            btn.closest('.item-row').remove();
            updateModeBadge();
        }
    } else {
        alert('Cannot remove the last item. At least one item is required.');
    }
}

document.getElementById('add-item')?.addEventListener('click', () => {
    const container = document.getElementById('items-container');
    const firstRow = document.querySelector('.item-row');
    const newRow = firstRow.cloneNode(true);
    
    newRow.setAttribute('data-row-index', itemIndex);
    
    // CRITICAL: Remove submission_item_id hidden field (new items shouldn't have this)
    newRow.querySelectorAll('input[name*="[submission_item_id]"]').forEach(input => {
        input.remove();
    });
    
    // Clear and update all other fields
    newRow.querySelectorAll('input, select, textarea').forEach(el => {
        if (el.name) {
            el.name = el.name.replace(/items\[\d+\]/, `items[${itemIndex}]`);
        }
        if (el.type !== 'file' && el.type !== 'hidden') {
            el.value = el.classList.contains('qty-input') ? 1 : '';
        }
    });
    
    // Remove all existing file references
    newRow.querySelectorAll('input[name*="[existing_files]"]').forEach(input => {
        input.remove();
    });
    
    // Clear file previews
    newRow.querySelector('#existing-files-' + (itemIndex - 1))?.remove();
    const existingFilesDiv = newRow.querySelector('[id^="existing-files-"]');
    if (existingFilesDiv) {
        existingFilesDiv.closest('.mb-3')?.remove();
    }
    
    newRow.querySelector('.new-files-preview').innerHTML = '';
    newRow.querySelector('.hidden-inputs-container').innerHTML = '';
    
    // Add remove button if not present
    const headerActions = newRow.querySelector('.item-row > div:first-child > button');
    if (!headerActions) {
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-sm btn-white border border-slate-200 rounded-2 fw-bold d-flex align-items-center gap-1';
        removeBtn.style.fontSize = '0.75rem';
        removeBtn.innerHTML = '<i class="fas fa-trash-alt text-danger"></i> Remove';
        removeBtn.onclick = function() { removeItemRow(this); };
        newRow.querySelector('.item-row > div:first-child').appendChild(removeBtn);
    }
    
    container.appendChild(newRow);
    itemIndex++;
    updateModeBadge();
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.category-select').forEach(sel => updateSubcats(sel, true));
    document.querySelectorAll('select[name*="[submission_type]"]').forEach(sel => toggleRowFields(sel));
    updateModeBadge();
});
</script>

@endsection