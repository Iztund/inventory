@extends('layouts.admin')

@section('title', 'System Structure | Inventory Management')

@section('content')

<div class="min-vh-100 py-4 px-3 px-lg-5 bg-slate-50">
<div style="max-width:1600px;" class="mx-auto">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
        <div>
            <h1 class="fw-black text-slate-900 mb-1" style="font-size:1.75rem; letter-spacing:-0.02em;">Organizational Structure</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0" style="font-size:0.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-decoration-none text-primary">Dashboard</a></li>
                    <li class="breadcrumb-item active text-slate-600 fw-bold">Hierarchy Management</li>
                </ol>
            </nav>
        </div>

        <div class="d-flex gap-2 align-items-center flex-wrap">
            <button type="button" class="btn btn-white border border-slate-200 d-flex align-items-center gap-2 rounded-3 shadow-sm"
                    style="font-size:0.8rem; padding:0.65rem 1.2rem;"
                    onclick="window.location='{{ route('admin.structure.export') }}'">
                <i class="fas fa-file-export text-slate-600"></i>
                <span class="fw-bold text-slate-700">Export</span>
            </button>
            
            <div class="bg-slate-900 text-white px-3 py-2 rounded-3 shadow-sm d-flex align-items-center gap-2" style="font-size:0.8rem;">
                <i class="fas fa-calendar-alt opacity-75"></i>
                <span class="fw-bold">{{ now()->format('M d, Y') }}</span>
            </div>
        </div>
    </div>

    {{-- Quick Stats Banner --}}
    <div class="rounded-4 mb-5 p-4 p-md-5 position-relative overflow-hidden" 
         style="background:linear-gradient(135deg, #0f172a 0%, #1e293b 60%, #334155 100%);">
        {{-- Decorative blobs --}}
        <div class="position-absolute rounded-circle" 
             style="top:-60px; right:-70px; width:240px; height:240px; background:radial-gradient(circle, rgba(245,158,11,0.15) 0%, transparent 70%); pointer-events:none;"></div>
        <div class="position-absolute rounded-circle" 
             style="bottom:-50px; left:5%; width:180px; height:180px; background:radial-gradient(circle, rgba(99,102,241,0.10) 0%, transparent 70%); pointer-events:none;"></div>
        
        <div class="position-relative" style="z-index:1;">
            <div class="row g-4">
                <div class="col-6 col-md-3">
                    <div class="text-slate-400 text-uppercase mb-1" style="font-size:0.65rem; letter-spacing:0.12em; font-weight:700;">Academic</div>
                    <div class="text-white fw-black" style="font-size:1.8rem; line-height:1;">{{ $summary['total_faculties'] + $summary['total_departments'] }}</div>
                    <div class="text-slate-400 mt-1" style="font-size:0.75rem;">Faculties & Depts</div>
                </div>
                
                <div class="col-6 col-md-3">
                    <div class="text-slate-400 text-uppercase mb-1" style="font-size:0.65rem; letter-spacing:0.12em; font-weight:700;">Administrative</div>
                    <div class="text-white fw-black" style="font-size:1.8rem; line-height:1;">{{ $summary['total_offices'] + $summary['total_units'] }}</div>
                    <div class="text-slate-400 mt-1" style="font-size:0.75rem;">Offices & Units</div>
                </div>
                
                <div class="col-6 col-md-3">
                    <div class="text-slate-400 text-uppercase mb-1" style="font-size:0.65rem; letter-spacing:0.12em; font-weight:700;">Research</div>
                    <div class="text-white fw-black" style="font-size:1.8rem; line-height:1;">{{ $summary['total_institutes'] }}</div>
                    <div class="text-slate-400 mt-1" style="font-size:0.75rem;">Institutes</div>
                </div>
                
                <div class="col-6 col-md-3">
                    <div class="text-slate-400 text-uppercase mb-1" style="font-size:0.65rem; letter-spacing:0.12em; font-weight:700;">Total Assets</div>
                    <div class="text-white fw-black" style="font-size:1.8rem; line-height:1;">{{ number_format($summary['total_global_assets']) }}</div>
                    <div class="text-slate-400 mt-1" style="font-size:0.75rem;">Registry-Wide</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Grid: Category Cards --}}
    <div class="row g-4">
        
        {{-- ACADEMIC CARD --}}
        <div class="col-12 col-lg-6">
            <div class="rounded-4 bg-white border border-slate-200 shadow-sm overflow-hidden h-100">
                <div class="p-4 bg-indigo-50 border-bottom border-indigo-100 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-indigo-600" 
                             style="width:48px; height:48px; box-shadow:0 4px 14px rgba(79,70,229,0.3);">
                            <i class="fas fa-graduation-cap text-white" style="font-size:1.2rem;"></i>
                        </div>
                        <div>
                            <h5 class="fw-black text-slate-900 mb-0" style="font-size:1.1rem;">Academic Branch</h5>
                            <p class="text-slate-600 mb-0" style="font-size:0.8rem;">Faculties & Departments</p>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-indigo text-white fw-bold rounded-2" 
                            style="font-size:0.75rem; padding:0.5rem 1rem;"
                            data-bs-toggle="modal" data-bs-target="#addAcademicModal">
                        <i class="fas fa-plus me-1"></i> Add
                    </button>
                </div>

                <div class="p-4">
                    <div class="d-grid gap-3">
                        {{-- Faculties --}}
                        <a href="{{ route('admin.faculties.index') }}" 
                           class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-slate-50 border border-slate-200 text-decoration-none"
                           style="transition:all 0.2s;"
                           onmouseenter="this.style.background='#eef2ff'; this.style.borderColor='#c7d2fe';"
                           onmouseleave="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-university text-indigo-600" style="font-size:0.95rem;"></i>
                                <span class="fw-bold text-slate-900" style="font-size:0.9rem;">Faculties</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill bg-indigo-100 text-indigo-700 fw-bold" style="padding:0.4rem 0.8rem;">
                                    {{ $summary['total_faculties'] }}
                                </span>
                                <i class="fas fa-chevron-right text-slate-400" style="font-size:0.7rem;"></i>
                            </div>
                        </a>

                        {{-- Departments --}}
                        <a href="{{ route('admin.departments.index') }}" 
                           class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-slate-50 border border-slate-200 text-decoration-none"
                           style="transition:all 0.2s;"
                           onmouseenter="this.style.background='#eef2ff'; this.style.borderColor='#c7d2fe';"
                           onmouseleave="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-building text-indigo-600" style="font-size:0.95rem;"></i>
                                <span class="fw-bold text-slate-900" style="font-size:0.9rem;">Departments</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill bg-indigo-100 text-indigo-700 fw-bold" style="padding:0.4rem 0.8rem;">
                                    {{ $summary['total_departments'] }}
                                </span>
                                <i class="fas fa-chevron-right text-slate-400" style="font-size:0.7rem;"></i>
                            </div>
                        </a>
                    </div>

                    {{-- Footer Stats --}}
                    <div class="row g-2 mt-3">
                        <div class="col-6">
                            <div class="p-3 rounded-3 bg-indigo-50 border border-indigo-100">
                                <div class="text-slate-600 fw-semibold mb-1" style="font-size:0.7rem;">Assets Assigned</div>
                                <div class="fw-black text-indigo-700" style="font-size:1rem;">{{ number_format($summary['assets_in_faculties'] + $summary['assets_in_departments']) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 bg-indigo-50 border border-indigo-100">
                                <div class="text-slate-600 fw-semibold mb-1" style="font-size:0.7rem;">Status</div>
                                <span class="badge bg-indigo-600 text-white fw-bold d-inline-block mt-1" style="font-size:0.7rem; padding:0.35rem 0.75rem;">
                                    {{ $summary['inactive_faculties'] > 0 ? 'Mixed' : 'All Active' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ADMINISTRATIVE CARD --}}
        <div class="col-12 col-lg-6">
            <div class="rounded-4 bg-white border border-slate-200 shadow-sm overflow-hidden h-100">
                <div class="p-4 bg-emerald-50 border-bottom border-emerald-100 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-emerald-600" 
                             style="width:48px; height:48px; box-shadow:0 4px 14px rgba(5,150,105,0.3);">
                            <i class="fas fa-user-shield text-white" style="font-size:1.2rem;"></i>
                        </div>
                        <div>
                            <h5 class="fw-black text-slate-900 mb-0" style="font-size:1.1rem;">Administrative Branch</h5>
                            <p class="text-slate-600 mb-0" style="font-size:0.8rem;">Offices & Units</p>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="d-grid gap-3">
                        {{-- Offices --}}
                        <a href="{{ route('admin.offices.index') }}" 
                           class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-slate-50 border border-slate-200 text-decoration-none"
                           style="transition:all 0.2s;"
                           onmouseenter="this.style.background='#f0fdf4'; this.style.borderColor='#bbf7d0';"
                           onmouseleave="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-briefcase text-emerald-600" style="font-size:0.95rem;"></i>
                                <span class="fw-bold text-slate-900" style="font-size:0.9rem;">Offices</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill bg-emerald-100 text-emerald-700 fw-bold" style="padding:0.4rem 0.8rem;">
                                    {{ $summary['total_offices'] }}
                                </span>
                                <i class="fas fa-chevron-right text-slate-400" style="font-size:0.7rem;"></i>
                            </div>
                        </a>

                        {{-- Units --}}
                        <a href="{{ route('admin.units.index') }}" 
                           class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-slate-50 border border-slate-200 text-decoration-none"
                           style="transition:all 0.2s;"
                           onmouseenter="this.style.background='#f0fdf4'; this.style.borderColor='#bbf7d0';"
                           onmouseleave="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-layer-group text-emerald-600" style="font-size:0.95rem;"></i>
                                <span class="fw-bold text-slate-900" style="font-size:0.9rem;">Units</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill bg-emerald-100 text-emerald-700 fw-bold" style="padding:0.4rem 0.8rem;">
                                    {{ $summary['total_units'] }}
                                </span>
                                <i class="fas fa-chevron-right text-slate-400" style="font-size:0.7rem;"></i>
                            </div>
                        </a>
                    </div>

                    {{-- Footer Stats --}}
                    <div class="row g-2 mt-3">
                        <div class="col-6">
                            <div class="p-3 rounded-3 bg-emerald-50 border border-emerald-100">
                                <div class="text-slate-600 fw-semibold mb-1" style="font-size:0.7rem;">Assets Assigned</div>
                                <div class="fw-black text-emerald-700" style="font-size:1rem;">{{ number_format($summary['assets_in_offices'] + $summary['assets_in_units']) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 bg-emerald-50 border border-emerald-100">
                                <div class="text-slate-600 fw-semibold mb-1" style="font-size:0.7rem;">Status</div>
                                <span class="badge bg-emerald-600 text-white fw-bold d-inline-block mt-1" style="font-size:0.7rem; padding:0.35rem 0.75rem;">
                                    {{ $summary['inactive_offices'] > 0 ? 'Mixed' : 'All Active' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RESEARCH CARD --}}
        <div class="col-12 col-lg-6">
            <div class="rounded-4 bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-4 bg-cyan-50 border-bottom border-cyan-100 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-cyan-600" 
                             style="width:48px; height:48px; box-shadow:0 4px 14px rgba(8,145,178,0.3);">
                            <i class="fas fa-microscope text-white" style="font-size:1.2rem;"></i>
                        </div>
                        <div>
                            <h5 class="fw-black text-slate-900 mb-0" style="font-size:1.1rem;">Research Branch</h5>
                            <p class="text-slate-600 mb-0" style="font-size:0.8rem;">Institutes & Centers</p>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="d-grid gap-3">
                        <a href="{{ route('admin.institutes.index') }}" 
                           class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-slate-50 border border-slate-200 text-decoration-none"
                           style="transition:all 0.2s;"
                           onmouseenter="this.style.background='#ecfeff'; this.style.borderColor='#a5f3fc';"
                           onmouseleave="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fas fa-flask text-cyan-600" style="font-size:0.95rem;"></i>
                                <span class="fw-bold text-slate-900" style="font-size:0.9rem;">Institutes</span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill bg-cyan-100 text-cyan-700 fw-bold" style="padding:0.4rem 0.8rem;">
                                    {{ $summary['total_institutes'] }}
                                </span>
                                <i class="fas fa-chevron-right text-slate-400" style="font-size:0.7rem;"></i>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="row g-2 mt-3">
                    <div class="col-6">
                        <div class="p-3 rounded-3 bg-emerald-50 border border-emerald-100">
                            <div class="text-slate-600 fw-semibold mb-1" style="font-size:0.7rem;">Assets Assigned</div>
                            <div class="fw-black text-emerald-700" style="font-size:1rem;">{{ number_format($summary['assets_in_institutes']) }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 rounded-3 bg-emerald-50 border border-emerald-100">
                            <div class="text-slate-600 fw-semibold mb-1" style="font-size:0.7rem;">Status</div>
                            <span class="badge bg-emerald-600 text-white fw-bold d-inline-block mt-1" style="font-size:0.7rem; padding:0.35rem 0.75rem;">
                                {{ $summary['inactive_institutes'] > 0 ? 'Mixed' : 'All Active' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

       {{-- INVENTORY CLASSIFICATION COMMAND CARD --}}
        <div class="col-12 col-lg-6">
            <div class="rounded-4 bg-white border border-slate-200 shadow-sm overflow-hidden h-100">
                <div class="p-4 bg-amber-50 border-bottom border-amber-100">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center bg-orange-500" style="width:48px; height:48px;">
                            <i class="fas fa-tags text-white"></i>
                        </div>
                        <div>
                            <h5 class="fw-black text-slate-900 mb-0 uppercase tracking-tight" style="font-size:1rem;">System Classification</h5>
                            <p class="text-slate-500 mb-0 font-bold uppercase" style="font-size:0.65rem;">Registry Hierarchy Manager</p>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="d-grid gap-3">
                        {{-- Category Row --}}
                        <div class="p-3 rounded-3 bg-slate-50 border border-slate-200">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="w-10 h-10 rounded bg-amber-100 text-amber-600 d-flex align-items-center justify-content-center"><i class="fas fa-folder"></i></div>
                                    <div>
                                        <span class="fw-bold text-slate-700 uppercase block" style="font-size:0.8rem;">Major Categories</span>
                                        <div class="d-flex gap-2">
                                            <a href="javascript:void(0)" onclick="openCategoryModal()" class="text-orange-600 fw-bold text-decoration-none" style="font-size:0.65rem;">+ ADD NEW</a>
                                            <span class="text-slate-300">|</span>
                                            <a href="{{ route('admin.classification_categories.index') }}" class="text-slate-500 fw-bold text-decoration-none" style="font-size:0.65rem;">MANAGE ALL</a>
                                        </div>
                                    </div>
                                </div>
                                <span class="badge rounded-pill bg-amber-100 text-amber-700 fw-black">{{ $summary['total_categories'] }}</span>
                            </div>
                        </div>

                        {{-- Subcategory Row --}}
                        <div class="p-3 rounded-3 bg-slate-50 border border-slate-200">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="w-10 h-10 rounded bg-blue-100 text-blue-600 d-flex align-items-center justify-content-center"><i class="fas fa-layer-group"></i></div>
                                    <div>
                                        <span class="fw-bold text-slate-700 uppercase block" style="font-size:0.8rem;">Sub-Classifications</span>
                                        <div class="d-flex gap-2">
                                            <a href="javascript:void(0)" onclick="openSubcategoryModal()" class="text-blue-600 fw-bold text-decoration-none" style="font-size:0.65rem;">+ ADD NEW</a>
                                            <span class="text-slate-300">|</span>
                                            <a href="{{ route('admin.classification_subcategories.index') }}" class="text-slate-500 fw-bold text-decoration-none" style="font-size:0.65rem;">MANAGE ALL</a>
                                        </div>
                                    </div>
                                </div>
                                <span class="badge rounded-pill bg-blue-100 text-blue-700 fw-black">{{ $summary['total_subcategories'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

@include('admin.Academic.partials.classifications_modals')
<style>
/* Input focus states */
.form-control:focus, .form-select:focus {
    background: #fff !important;
    border-color: #0f172a !important;
    box-shadow: 0 0 0 3px rgba(15,23,42,0.08) !important;
}

/* Modal animations */
.modal.fade .modal-dialog {
    transform: scale(0.95);
    transition: transform 0.2s ease-out;
}
.modal.show .modal-dialog {
    transform: scale(1);
}
</style>

<script>
    function openCategoryModal(id = null, name = '') {
        const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
        document.getElementById('categoryModalTitle').innerText = id ? 'Edit Category' : 'New Category';
        document.getElementById('categoryForm').action = id ? `/admin/classifications/categories/${id}` : "{{ route('admin.categories.store') }}";
        document.getElementById('categoryMethod').value = id ? "PUT" : "POST";
        document.getElementById('category_name_input').value = name;
        modal.show();
    }

    function openSubcategoryModal(id = null, name = '', parentId = '') {
        const modal = new bootstrap.Modal(document.getElementById('subcategoryModal'));
        document.getElementById('subcategoryModalTitle').innerText = id ? 'Edit Sub-Classification' : 'New Sub-Classification';
        document.getElementById('subcategoryForm').action = id ? `/admin/classifications/subcategories/${id}` : "{{ route('admin.subcategories.store') }}";
        document.getElementById('subcategoryMethod').value = id ? "PUT" : "POST";
        document.getElementById('subcategory_name_input').value = name;
        document.getElementById('sub_parent_id').value = parentId;
        modal.show();
    }

    function confirmDelete(id, name, type) {
        if (confirm(`Are you sure you want to delete ${name}? This will check for linked medical assets first.`)) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/classifications/${type}/${id}`;
            form.innerHTML = `@csrf @method('DELETE')`;
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>

@endsection