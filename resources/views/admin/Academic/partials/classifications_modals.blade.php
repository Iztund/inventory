{{-- Classification Modal --}}

<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden">
            <form id="categoryForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="categoryMethod" value="POST">
                
                <div class="p-4 bg-amber-500 text-white d-flex justify-content-between align-items-center">
                    <h5 class="fw-black uppercase mb-0" style="font-size:0.9rem;" id="categoryModalTitle">New Category</h5>
                    <i class="fas fa-folder"></i>
                </div>
                
                <div class="p-4">
                    <div class="mb-0">
                        <label class="text-xs font-black text-slate-400 uppercase mb-2 block">Category Name</label>
                        <input type="text" name="category_name" id="category_name_input" 
                               class="form-control bg-slate-50 border-slate-200 py-2 text-sm rounded-3" 
                               placeholder="e.g. Medical Consumables" required>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 d-flex justify-content-end gap-2">
                    <button type="button" class="btn text-xs font-black text-slate-400 uppercase" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn bg-amber-600 text-white text-xs font-black px-4 rounded-lg shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="subcategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden">
            <form id="subcategoryForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="subcategoryMethod" value="POST">
                
                <div class="p-4 bg-blue-600 text-white d-flex justify-content-between align-items-center">
                    <h5 class="fw-black uppercase mb-0" style="font-size:0.9rem;" id="subcategoryModalTitle">New Sub-Classification</h5>
                    <i class="fas fa-layer-group"></i>
                </div>
                
                <div class="p-4">
                    <div class="mb-3">
                        <label class="text-xs font-black text-slate-400 uppercase mb-2 block">Parent Category</label>
                        <select name="category_id" id="sub_parent_id" class="form-select bg-slate-50 border-slate-200 text-sm rounded-3" required>
                            <option value="">-- Select Parent --</option>
                            @foreach($dropdownData['categories'] as $cat)
                                <option value="{{ $cat->category_id }}">{{ $cat->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="text-xs font-black text-slate-400 uppercase mb-2 block">Subcategory Name</label>
                        <input type="text" name="subcategory_name" id="subcategory_name_input" 
                               class="form-control bg-slate-50 border-slate-200 py-2 text-sm rounded-3" 
                               placeholder="e.g. Disposable Syringes" required>
                    </div>
                </div>

                <div class="p-3 bg-slate-50 d-flex justify-content-end gap-2">
                    <button type="button" class="btn text-xs font-black text-slate-400 uppercase" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn bg-blue-700 text-white text-xs font-black px-4 rounded-lg shadow-sm">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>