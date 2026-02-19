@extends('layouts.staff')

@section('title', 'New Inventory Submission')

@section('content')

<div class="container-fluid py-6 px-4 max-w-[1600px] mx-auto">
    
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('staff.submissions.index') }}" 
                   class="w-10 h-10 flex items-center justify-center rounded-full bg-white border border-slate-200 shadow-sm text-slate-400 hover:bg-slate-900 hover:text-white hover:border-slate-900 transition-all duration-200 no-underline">
                    <i class="fas fa-arrow-left text-xs"></i>
                </a>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight mb-0">New Inventory Submission</h1>
            </div>
            <p class="text-sm text-slate-500 ml-12">Submit assets for audit and verification</p>
        </div>
    </div>

    <form action="{{ route('staff.submissions.store') }}" method="POST" enctype="multipart/form-data" id="audit-form">
        @csrf
        <div class="row g-4">
            
            {{-- Left Column: Asset Items --}}
            <div class="col-lg-8">
                <div id="items-container">
                    {{-- The First Row (Template for cloning) --}}
                    <div class="item-row bg-white rounded-2xl border border-slate-200 shadow-sm mb-6 overflow-hidden transition-all duration-300 relative" data-row-index="0">
                        
                        {{-- Item Header --}}
                        <div class="flex justify-between items-center px-6 py-4 bg-slate-50 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center">
                                    <i class="fas fa-box text-white text-xs"></i>
                                </div>
                                <span class="item-number-label font-black text-slate-900 text-sm">Asset Entry #1</span>
                            </div>
                            {{-- Placeholder for JS to inject delete button on cloned rows --}}
                            <div class="delete-btn-placeholder"></div>
                        </div>

                        <div class="p-6">
                            {{-- Entry Type & Selection Section --}}
                            <div class="bg-emerald-50/50 rounded-xl p-4 border border-emerald-100 mb-6">
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="form-label font-bold text-slate-700 text-[11px] uppercase tracking-wider">Entry Type</label>
                                        <select name="items[0][submission_type]" class="form-select submission-type-select rounded-lg border-slate-200 text-sm shadow-sm focus:border-emerald-500 focus:ring-emerald-500" onchange="toggleRowFields(this)">
                                            <option value="new_purchase">New Purchase</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="transfer">Internal Transfer</option>
                                            <option value="disposal">Disposal</option>
                                        </select>
                                    </div>

                                    {{-- Existing Asset Selection --}}
                                    {{-- Existing Asset Selection (Shown for Maintenance, Transfer, Disposal) --}}
                                    <div class="col-md-7 asset-selection-area" style="display: none;">
                                        <label class="form-label font-bold text-slate-700 text-[11px] uppercase tracking-wider">Search Asset (Name or Tag)</label>
                                        {{-- Added 'asset-search-select' class --}}
                                        <select name="items[0][asset_id]" 
                                            class="form-select asset-search-select rounded-lg" 
                                            onchange="autoFillAssetDetails(this)">
                                        <option value="">-- Choose Asset --</option>
                                        @foreach($assets as $asset)
                                            <option value="{{ $asset->asset_id }}" 
                                                    data-name="{{ $asset->item_name }}"
                                                    data-cat="{{ $asset->category_id }}"
                                                    data-subcat="{{ $asset->subcategory_id }}">
                                                {{ $asset->asset_tag }} - {{ $asset->item_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Main Asset Details --}}
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label font-bold text-slate-700 text-[11px] uppercase tracking-wider"><i class="fas fa-tags text-slate-400 mr-1"></i> Category</label>
                                    <select name="items[0][category_id]" class="form-select category-select rounded-lg border-slate-200 text-sm shadow-sm" onchange="updateSubcats(this)" required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->category_id }}">{{ $cat->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label font-bold text-slate-700 text-[11px] uppercase tracking-wider"><i class="fas fa-layer-group text-slate-400 mr-1"></i> Sub-Category</label>
                                    <select name="items[0][subcategory_id]" class="form-select subcategory-select rounded-lg border-slate-200 text-sm shadow-sm" required>
                                        <option value="">-- Select Category First --</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label font-bold text-slate-700 text-[11px] uppercase tracking-wider"><i class="fas fa-box text-slate-400 mr-1"></i> Item Name / Model Description</label>
                                    <input type="text" name="items[0][item_name]" class="form-control rounded-lg border-slate-200 text-sm shadow-sm" placeholder="e.g., Dell Latitude 5420 Laptop" required>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label font-bold text-slate-700 text-[11px] uppercase tracking-wider"><i class="fas fa-hashtag text-slate-400 mr-1"></i> Quantity</label>
                                    <input type="number" name="items[0][quantity]" value="1" min="1" class="form-control qty-input rounded-lg border-slate-200 text-sm shadow-sm" oninput="runGrandTotal()">
                                </div>

                                <div class="col-md-4 cost-area">
                                    <label class="form-label font-bold text-slate-700 text-[11px] uppercase tracking-wider"><i class="fas fa-naira-sign text-slate-400 mr-1"></i> Unit Cost (₦)</label>
                                    <input type="number" step="0.01" name="items[0][cost]" class="form-control cost-input rounded-lg border-slate-200 text-sm shadow-sm" placeholder="0.00" oninput="runGrandTotal()">
                                </div>

                                <div class="col-md-4 serial-area">
                                    <label class="form-label font-bold text-slate-700 text-[11px] uppercase tracking-wider"><i class="fas fa-barcode text-slate-400 mr-1"></i> Serial Number</label>
                                    <input type="text" name="items[0][serial_number]" class="form-control rounded-lg border-slate-200 text-sm shadow-sm" placeholder="Optional">
                                </div>

                                <div class="col-12">
                                    <label class="form-label font-bold text-emerald-700 text-[11px] uppercase tracking-wider">Item Notes</label>
                                    <textarea name="items[0][item_notes]" rows="2" class="form-control rounded-lg border-slate-200 text-sm shadow-sm" placeholder="Technical details or condition..."></textarea>
                                </div>

                                {{-- File Upload --}}
                                <div class="col-12 mt-4">
                                    <label class="form-label font-bold text-slate-700 text-[11px] uppercase tracking-wider block mb-3">
                                        <i class="fas fa-camera text-slate-400 mr-1"></i> Supporting Evidence
                                    </label>
                                    
                                    <div class="border-2 border-dashed border-slate-200 rounded-xl p-6 bg-slate-50/50 text-center hover:bg-emerald-50 hover:border-emerald-200 transition-all cursor-pointer relative" 
                                        onclick="this.querySelector('.trigger-input').click()">
                                        
                                        <input type="file" class="hidden trigger-input" multiple accept="image/*,.pdf" onchange="handleFileSelect(this)">
                                        <i class="fas fa-cloud-upload-alt text-slate-300 text-3xl mb-2"></i>
                                        <p class="text-sm font-bold text-slate-600 mb-1">Click to upload or drag files here</p>
                                        <p class="text-xs text-slate-400">JPG, PNG, PDF • Max 10MB per file</p>
                                        
                                        {{-- Hidden storage for JS --}}
                                        <div class="hidden-inputs-container hidden"></div>
                                    </div>

                                    <div class="file-preview-container flex flex-wrap gap-3 mt-4 justify-start"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="button" id="add-row" class="w-100 py-4 border-2 border-dashed border-slate-200 rounded-2xl bg-white text-slate-500 font-bold text-sm hover:bg-slate-50 hover:text-emerald-600 hover:border-emerald-200 transition-all mb-8">
                    <i class="fas fa-plus-circle mr-2"></i> Add Another Submission Entry
                </button>
            </div>

            {{-- Right Column: Summary --}}
            <div class="col-lg-4">
                <div class="sticky top-24">
                    <div class="rounded-2xl shadow-xl mb-6 bg-gradient-to-br from-emerald-600 to-emerald-800 p-8 text-center text-white">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-100 mb-2">Total Submission Value</p>
                        <h2 class="text-4sm font-black tracking-tight mb-2">₦<span id="grand-total-val">0.00</span></h2>
                        <p class="text-[10px] text-emerald-200/80 italic"><i class="fas fa-info-circle mr-1"></i> Includes New Purchases & Repairs</p>
                    </div>

                    {{-- Submission Meta --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 font-black text-slate-900 text-[11px] uppercase tracking-widest">Submission Details</div>
                        <div class="p-6">
                            <div class="mb-4">
                                <label class="form-label font-bold text-slate-700 text-[11px] uppercase">Overall Notes</label>
                                <textarea name="notes" rows="3" class="form-control rounded-lg border-slate-200 text-sm shadow-sm" placeholder="General context..."></textarea>
                            </div>
                            <div class="mb-0">
                                <label class="form-label font-bold text-slate-700 text-[11px] uppercase">Executive Summary</label>
                                <textarea name="summary" rows="3" class="form-control rounded-lg border-slate-200 text-sm shadow-sm" placeholder="Brief summary for committee..."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Entity Display Card --}}
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-6">
                        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 font-black text-slate-900 text-[11px] uppercase tracking-widest">Submitting Entity</div>
                        <div class="p-6">
                            @php $u = auth()->user(); @endphp
                            @foreach(['faculty', 'institute', 'department', 'office', 'unit'] as $entity)
                                @if($u && $u->$entity)
                                    <div class="flex items-center gap-4 mb-4 last:mb-0 pb-4 last:pb-0 border-b last:border-0 border-slate-100">
                                        <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 flex-shrink-0">
                                            <i class="fas fa-{{ $entity == 'faculty' ? 'graduation-cap' : ($entity == 'institute' ? 'university' : ($entity == 'department' ? 'building-columns' : ($entity == 'office' ? 'briefcase' : 'microscope'))) }}"></i>
                                        </div>
                                        <div>
                                            <p class="text-[9px] font-black uppercase text-slate-400 tracking-tighter mb-0">{{ $entity }}</p>
                                            <p class="text-sm font-bold text-slate-900 leading-tight">{{ $u->$entity->{$entity.'_name'} }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" id="submitBtn" class="btn btn-primary bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-bold transition-all flex items-center gap-2">
                        <span id="btnText">Submit Item(s)</span>
                        <span id="btnSpinner" class="hidden animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .preview-item {
        position: relative;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 0.75rem;
        padding: 0.25rem;
        width: 5rem;
        height: 5rem;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    }
    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 0.5rem;
    }
</style>

<script>
const subMap = {!! json_encode($subcategoryMap) !!};
let itemIndex = 1; 
let rowFilesTracker = { 0: [] }; 

/**
 * Handle File Selection & Previews
 */
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
    if (!row) return;

    const previewZone = row.querySelector('.file-preview-container');
    const hiddenZone = row.querySelector('.hidden-inputs-container');
    previewZone.innerHTML = ''; 
    hiddenZone.innerHTML = '';
    
    const dt = new DataTransfer();
    
    if (rowFilesTracker[idx] && rowFilesTracker[idx].length > 0) {
        rowFilesTracker[idx].forEach((file, fileIdx) => {
            dt.items.add(file);
            
            const isImage = file.type.startsWith('image/');
            const div = document.createElement('div');
            
            // "Pill" Style: Better for long filenames
            div.className = 'flex items-center bg-white border border-slate-200 rounded-lg p-2 pr-10 relative shadow-sm max-w-[200px] overflow-hidden';
            
            div.innerHTML = `
                <div class="w-8 h-8 flex-shrink-0 mr-3 overflow-hidden rounded bg-slate-100 flex items-center justify-center">
                    ${isImage 
                        ? `<img src="${URL.createObjectURL(file)}" class="w-full h-full object-cover">` 
                        : `<i class="fas fa-file-pdf text-red-500 text-sm"></i>`}
                </div>

                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-medium text-slate-700 truncate" title="${file.name}">
                        ${file.name}
                    </p>
                    <p class="text-[8px] text-slate-400 uppercase">${(file.size / 1024).toFixed(1)} KB</p>
                </div>

                <button type="button" 
                    onclick="event.stopPropagation(); removeRowFile(${idx}, ${fileIdx})" 
                    class="absolute right-1 top-1/2 -translate-y-1/2 w-6 h-6 flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors">
                    <i class="fas fa-times-circle"></i>
                </button>
            `;
            previewZone.appendChild(div);
        });
    }
    
    // Create the hidden input for form submission
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'file'; 
    hiddenInput.name = `items[${idx}][documents][]`; 
    hiddenInput.multiple = true; 
    hiddenInput.files = dt.files;
    hiddenInput.classList.add('hidden');
    hiddenZone.appendChild(hiddenInput);
}
/**
 * Category & Asset Auto-Fill Logic
 */
function updateSubcats(sel) {
    const row = sel.closest('.item-row');
    const subSel = row.querySelector('.subcategory-select');
    const catId = sel.value;
    
    subSel.innerHTML = '<option value="">-- Select Sub-Category --</option>';
    
    if (catId && subMap[catId]) {
        const options = Object.values(subMap[catId]);
        options.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.subcategory_id;
            opt.textContent = s.subcategory_name;
            subSel.appendChild(opt);
        });
    }
}

function autoFillAssetDetails(selectEl) {
    const selectedOption = selectEl.options[selectEl.selectedIndex];
    if (!selectedOption || !selectedOption.value) return;

    const row = selectEl.closest('.item-row');
    const nameInput = row.querySelector('input[name*="[item_name]"]');
    if (nameInput) nameInput.value = selectedOption.getAttribute('data-name');

    const catSelect = row.querySelector('.category-select');
    if (catSelect) {
        catSelect.value = selectedOption.getAttribute('data-cat');
        updateSubcats(catSelect);
        setTimeout(() => {
            const subcatSelect = row.querySelector('.subcategory-select');
            if (subcatSelect) subcatSelect.value = selectedOption.getAttribute('data-subcat');
        }, 100);
    }
}

/**
 * UI Toggle & Totals
 */
function toggleRowFields(sel) {
    const row = sel.closest('.item-row');
    const type = sel.value;
    
    row.querySelector('.cost-area').style.display = (type === 'new_purchase' || type === 'maintenance') ? 'block' : 'none';
    row.querySelector('.serial-area').style.display = (type === 'new_purchase') ? 'block' : 'none';
    
    const assetSelect = row.querySelector('.asset-selection-area');
    if(assetSelect) {
        assetSelect.style.display = (type !== 'new_purchase') ? 'block' : 'none';
        const selectEl = assetSelect.querySelector('select');
        type !== 'new_purchase' ? selectEl.setAttribute('required', 'required') : selectEl.removeAttribute('required');
    }
    runGrandTotal();
}

function runGrandTotal() {
    let grand = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const type = row.querySelector('.submission-type-select').value;
        if (type === 'new_purchase' || type === 'maintenance') {
            const q = parseFloat(row.querySelector('.qty-input').value) || 0;
            const c = parseFloat(row.querySelector('.cost-input').value) || 0;
            grand += (q * c);
        }
    });
    const display = document.getElementById('grand-total-val');
    if (display) display.textContent = grand.toLocaleString('en-US', {minimumFractionDigits: 2});
}

/**
 * Row Management (Add/Remove)
 */
document.getElementById('add-row').addEventListener('click', () => {
    const container = document.getElementById('items-container');
    const allRows = document.querySelectorAll('.item-row');
    const firstRow = allRows[0];
    const newRow = firstRow.cloneNode(true);
    
    // 1. Calculate the new index based on current length
    const newIdx = allRows.length;
    
    // 2. IMPORTANT: Initialize a completely empty array for the new index
    rowFilesTracker[newIdx] = []; 
    
    // 3. Scrub the cloned row of any Row 0 data
    newRow.setAttribute('data-row-index', newIdx);
    newRow.querySelector('.file-preview-container').innerHTML = '';
    newRow.querySelector('.hidden-inputs-container').innerHTML = '';
    
    // 4. Reset standard inputs
    newRow.querySelectorAll('input, select, textarea').forEach(input => {
        if (input.type !== 'file') {
            input.value = input.classList.contains('qty-input') ? 1 : '';
        } else {
            // Physically clear the visible file input as well
            input.value = ''; 
        }
    });

    container.appendChild(newRow);

    // 5. Re-run renumbering to fix names/IDs
    renumberAll();
});

function removeRow(idx, btn) {
    btn.closest('.item-row').remove();
    runGrandTotal();
    renumberAll();
}

function renumberAll() {
    const rows = document.querySelectorAll('.item-row');
    const newTracker = {}; 

    rows.forEach((row, i) => {
        const oldIdx = row.getAttribute('data-row-index');
        row.setAttribute('data-row-index', i);

        // Move the file array to the new index
        newTracker[i] = rowFilesTracker[oldIdx] || [];

        // Fix names
        row.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/items\[\d+\]/, `items[${i}]`);
        });

        // RE-SYNC: This is what fixes the visual and data ghosting
        syncRowFiles(i); 
    });

    rowFilesTracker = newTracker;
}
document.querySelector('form').addEventListener('submit', function(e) {
    let isValid = true;
    let errorMessage = "";

    document.querySelectorAll('.item-row').forEach((row, i) => {
        // Ensure the hidden inputs are perfectly synced with the JS tracker before sending
        if (typeof syncRowFiles === "function") {
            syncRowFiles(i);
        }

        const type = row.querySelector('.submission-type-select').value;
        const itemNameInput = row.querySelector('input[name*="[item_name]"]');
        const itemName = itemNameInput ? itemNameInput.value : `Item #${i+1}`;
        
        // Medical Validation: Must have documentation for audit purposes
        if ((type === 'new_purchase' || type === 'maintenance')) {
            if (!rowFilesTracker[i] || rowFilesTracker[i].length === 0) {
                isValid = false;
                errorMessage += `• Row #${i+1} (${itemName}) requires a Receipt or Invoice.\n`;
                row.classList.add('border-red-500', 'bg-red-50'); 
            } else {
                row.classList.remove('border-red-500', 'bg-red-50');
            }
        }
    });

    if (!isValid) {
        e.preventDefault();
        // Professional Alert
        alert("Action Required: Missing Evidence\n\n" + errorMessage);
    } else {
        // --- SPINNER LOGIC ---
        const btn = document.querySelector('button[type="submit"]');
        
        // Prevent double-clicking (very important when uploading heavy medical manuals/PDFs)
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        
        // Using Tailwind for the spinner to maintain your styling
        btn.innerHTML = `
            <svg class="animate-spin h-5 w-5 mr-3 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Processing Upload...
        `;
    }
});
</script>

@endsection