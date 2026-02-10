@extends('layouts.admin')

@section('content')
<style>
    /* Modern UI Refinements */
    .btn-control {
        background-color: white !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    
    /* Hover state for Edit button */
    .btn-control-edit:hover {
        background-color: #fff7ed !important; /* orange-50 */
        transform: translateY(-1px);
    }
    .btn-control-edit:hover i {
        color: #f97316 !important; /* orange-500 */
    }

    /* Hover state for Delete button */
    .btn-control-delete:hover {
        background-color: #fff1f2 !important; /* rose-50 */
        transform: translateY(-1px);
    }
    .btn-control-delete:hover i {
        color: #e11d48 !important; /* rose-600 */
    }

    /* Breadcrumb styling */
    .breadcrumb-item + .breadcrumb-item::before {
        content: "›";
        color: #cbd5e1;
        font-weight: bold;
    }
</style>

<div class="min-h-screen bg-slate-50/50 pb-5">
    {{-- Modern Header Section --}}
    <div class="bg-white border-b border-slate-200 mb-4 shadow-sm">
        <div class="container-fluid py-4 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight mb-1">Registry Intelligence</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item text-slate-400 text-xs font-bold uppercase tracking-wider">Dashboard</li>
                            <li class="breadcrumb-item active text-orange-500 text-xs font-bold uppercase tracking-wider">Classification Manager</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="openCategoryModal()" class="btn btn-dark btn-sm rounded-3 px-3 py-2 fw-black text-xs uppercase tracking-widest shadow-sm">
                        <i class="fas fa-plus me-2"></i> New Category
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-3 px-3 py-2 fw-black text-xs uppercase tracking-widest">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4">
        {{-- Unified Search & Tab Card --}}
        <div class="bg-white rounded-4 border border-slate-200 shadow-sm p-3 mb-4">
            <div class="row align-items-center g-3">
                <div class="col-md-6">
                    <ul class="nav nav-pills bg-slate-100 p-1 rounded-3 d-inline-flex border border-slate-200" id="classificationTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active rounded-2 px-4 py-2 fw-black text-xs uppercase tracking-tighter" data-bs-toggle="pill" data-bs-target="#categories-pane">
                                <i class="fas fa-folder me-2"></i>Categories
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-2 px-4 py-2 fw-black text-xs uppercase tracking-tighter" data-bs-toggle="pill" data-bs-target="#subcategories-pane">
                                <i class="fas fa-layer-group me-2"></i>Sub-Class
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <div class="position-relative w-100">
                        <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-slate-400"></i>
                        <input type="text" id="globalSearch" class="form-control ps-5 rounded-3 border-slate-200 py-2.5 text-sm focus:ring-2 focus:ring-orange-500/20 shadow-none" 
                            placeholder="Type to filter medical registry...">
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content">
            {{-- CATEGORIES PANE --}}
            <div class="tab-pane fade show active" id="categories-pane">
                <div class="bg-white border border-slate-200 rounded-4 shadow-sm overflow-hidden">
                    <table class="table align-middle mb-0">
                        <thead class="bg-slate-50 border-bottom border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-slate-400 uppercase fw-black text-[10px] tracking-widest">Major Classification</th>
                                <th class="px-4 py-3 text-slate-400 uppercase fw-black text-[10px] tracking-widest text-center">Depth</th>
                                <th class="px-4 py-3 text-slate-400 uppercase fw-black text-[10px] tracking-widest text-end">Control</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($categories as $cat)
                            <tr class="hover:bg-slate-50/50 transition-all duration-200">
                                <td class="px-4 py-4">
                                    <div class="d-flex align-items-center">
                                        <div class="w-8 h-8 rounded-2 bg-amber-50 text-amber-600 d-flex align-items-center justify-content-center me-3 border border-amber-100">
                                            <i class="fas fa-folder-open text-xs"></i>
                                        </div>
                                        <span class="fw-black text-slate-800 tracking-tight">{{ $cat->category_name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="badge rounded-pill bg-slate-100 text-slate-600 px-3 py-2 font-black text-[10px] border border-slate-200">
                                        {{ $cat->subcategories_count ?? 0 }} UNITS
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-end">
                                    <div class="btn-group shadow-sm rounded-2 overflow-hidden border border-slate-200">
                                        <button onclick="openCategoryModal('{{ $cat->category_id }}', '{{ $cat->category_name }}')" 
                                                class="btn btn-control btn-control-edit btn-sm px-3 border-0">
                                            <i class="fas fa-pen text-slate-400"></i>
                                        </button>
                                        <button onclick="confirmDelete('{{ $cat->category_id }}', '{{ $cat->category_name }}', 'categories')" 
                                                class="btn btn-control btn-control-delete btn-sm px-3 border-0 border-start border-slate-200">
                                            <i class="fas fa-trash text-rose-400"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- SUBCATEGORIES PANE --}}
            <div class="tab-pane fade" id="subcategories-pane">
                <div class="bg-white border border-slate-200 rounded-4 shadow-sm overflow-hidden">
                    <table class="table align-middle mb-0">
                        <thead class="bg-slate-50 border-bottom border-slate-200">
                            <tr>
                                <th class="px-4 py-3 text-slate-400 uppercase fw-black text-[10px] tracking-widest">Sub-Classification</th>
                                <th class="px-4 py-3 text-slate-400 uppercase fw-black text-[10px] tracking-widest">Parent Group</th>
                                <th class="px-4 py-3 text-slate-400 uppercase fw-black text-[10px] tracking-widest text-end">Control</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($subcategories as $sub)
                            <tr class="hover:bg-slate-50/50 transition-all duration-200">
                                <td class="px-4 py-4">
                                    <span class="fw-black text-slate-800 tracking-tight">{{ $sub->subcategory_name }}</span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="text-xs fw-black uppercase text-blue-600 px-2 py-1 rounded bg-blue-50 border border-blue-100">
                                        {{ $sub->category->category_name ?? 'Unlinked' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-end">
                                    <div class="btn-group shadow-sm rounded-2 overflow-hidden border border-slate-200">
                                        <button onclick="openSubcategoryModal('{{ $sub->subcategory_id }}', '{{ $sub->subcategory_name }}', '{{ $sub->category_id }}')" 
                                                class="btn btn-control btn-control-edit btn-sm px-3 border-0">
                                            <i class="fas fa-pen text-slate-400"></i>
                                        </button>
                                        <button onclick="confirmDelete('{{ $sub->subcategory_id }}', '{{ $sub->subcategory_name }}', 'subcategories')" 
                                                class="btn btn-control btn-control-delete btn-sm px-3 border-0 border-start border-slate-200">
                                            <i class="fas fa-trash text-rose-400"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Standard Delete Form --}}
<form id="deleteForm" method="POST" class="d-none">
    @csrf
    @method('DELETE')
</form>

@include('admin.Academic.partials.classifications_modals')

{{-- Full JavaScript Suite --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // 1. CATEGORY MODAL LOGIC (ADD/EDIT)
    function openCategoryModal(id = '', name = '') {
        const form = document.getElementById('categoryForm');
        const title = document.getElementById('categoryModalTitle');
        const method = document.getElementById('categoryMethod');
        const input = document.getElementById('category_name_input');

        if (id) {
            title.innerText = 'Modify Classification';
            form.action = `/admin/categories/${id}`;
            method.value = 'PUT';
            input.value = name;
        } else {
            title.innerText = 'New Major Category';
            form.action = "{{ route('admin.categories.store') }}";
            method.value = 'POST';
            input.value = '';
        }

        new bootstrap.Modal(document.getElementById('categoryModal')).show();
    }

    // 2. SUBCATEGORY MODAL LOGIC (ADD/EDIT)
    function openSubcategoryModal(id = '', name = '', parentId = '') {
        const form = document.getElementById('subcategoryForm');
        const title = document.getElementById('subcategoryModalTitle');
        const method = document.getElementById('subcategoryMethod');
        const nameInput = document.getElementById('subcategory_name_input');
        const parentSelect = document.getElementById('sub_parent_id');

        if (id) {
            title.innerText = 'Modify Sub-Classification';
            form.action = `/admin/subcategories/${id}`;
            method.value = 'PUT';
            nameInput.value = name;
            parentSelect.value = parentId;
        } else {
            title.innerText = 'New Sub-Classification';
            form.action = "{{ route('admin.subcategories.store') }}";
            method.value = 'POST';
            nameInput.value = '';
            parentSelect.value = '';
        }

        new bootstrap.Modal(document.getElementById('subcategoryModal')).show();
    }

    // 3. DELETE CONFIRMATION
    function confirmDelete(id, name, type) {
        Swal.fire({
            title: 'Delete Record?',
            text: `Confirming deletion of "${name}". This action is permanent.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = `/admin/${type}/${id}`;
                form.submit();
            }
        });
    }

    // 4. LIVE SEARCH FILTER
    document.getElementById('globalSearch').addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        const activePane = document.querySelector('.tab-pane.show.active');
        const rows = activePane.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(query) ? "" : "none";
        });
    });
</script>
@endsection

