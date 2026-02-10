@extends('layouts.auditor')

@section('title', 'Full Asset Registry')

@section('content')
<div class="container-fluid px-3 sm:px-4 lg:px-6 py-4 lg:py-6 min-vh-100 bg-white">
    
    {{-- 1. PREMIUM HEADER SECTION --}}
    <div class="row mb-4 mb-md-5 px-3 px-md-4">
        <div class="col-md-7">
            <h1 class="fw-black text-slate-900 mb-0 tracking-tighter uppercase leading-none" style="font-size: clamp(1.8rem, 5vw, 2.8rem);">Full Registry</h1>
            <div class="flex flex-wrap items-center gap-2 mt-2">
                <div class="flex items-center">
                    <div class="relative flex items-center justify-center w-3 h-3 me-2">
                        <div class="absolute inset-0 bg-emerald-500 rounded-full animate-ping opacity-25"></div>
                        <div class="relative w-2 h-2 bg-emerald-500 rounded-full"></div>
                    </div>
                    <span class="bg-slate-900 text-white text-[9px] md:text-[10px] fw-black px-3 py-1 rounded-full uppercase tracking-widest">Master Audit History</span>
                </div>
                <span class="text-slate-400 fw-bold text-[10px] md:text-[11px] uppercase tracking-widest">College of Medicine Inventory</span>
            </div>
        </div>
        <div class="col-md-5 text-md-end self-center mt-4 mt-md-0">
            <div class="bg-indigo-600 rounded-2xl p-3 shadow-lg shadow-indigo-200 inline-block text-left min-w-[220px]">
                <span class="block text-[10px] font-black text-indigo-200 uppercase tracking-widest leading-none mb-1">Total Approved Value</span>
                <span class="block text-xl md:text-2xl font-black text-white tracking-tighter">₦{{ number_format($total_registry_value, 2) }}</span>
            </div>
        </div>
    </div>

    {{-- 2. COMMAND CENTER FILTERS --}}
    <div class="mb-6 mb-md-8 px-3 px-md-4">
        <div class="bg-white rounded-[1.5rem] md:rounded-[2rem] shadow-sm border-t-4 border-slate-900 overflow-hidden">
            <div class="p-4 md:p-6">
                <form action="{{ route('auditor.registry.index') }}" method="GET" id="filterForm">
                    <div class="row g-3">
                        {{-- Search Bar --}}
                        <div class="col-12">
                            <div class="flex flex-col md:flex-row gap-2 bg-slate-50 border border-slate-200 rounded-2xl p-1 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                                <div class="flex items-center flex-grow px-3 py-2">
                                    <span class="text-slate-400"><i class="fas fa-search"></i></span>
                                    <input type="text" name="search" 
                                           class="w-full border-0 bg-transparent px-3 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:ring-0" 
                                           placeholder="Search Submission ID, Serial No, or Item Name..." 
                                           value="{{ request('search') }}">
                                </div>
                                <div class="flex items-center gap-2 px-2 border-l border-slate-200">
                                    <input type="date" name="start_date" class="form-control form-control-sm border-0 bg-transparent text-[11px] font-bold" value="{{ request('start_date') }}">
                                    <span class="text-slate-400 text-[10px] font-black">TO</span>
                                    <input type="date" name="end_date" class="form-control form-control-sm border-0 bg-transparent text-[11px] font-bold" value="{{ request('end_date') }}">
                                </div>
                                <button type="submit" class="w-full md:w-auto bg-slate-900 text-white px-8 py-3 rounded-xl md:rounded-r-2xl font-black text-xs uppercase tracking-widest hover:bg-indigo-600 transition-colors">
                                    REFRESH VIEW
                                </button>
                            </div>
                        </div>

                        {{-- Entity Filters --}}
                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 h-full shadow-sm">
                                <h6 class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-2 italic">Academic Branch</h6>
                                <select name="faculty_id" id="facultySelect" class="form-select form-select-sm border-slate-200 rounded-lg text-[11px] font-bold mb-2">
                                    <option value="">-- Faculty --</option>
                                    @foreach($faculties as $f)
                                        <option value="{{ $f->faculty_id }}" {{ request('faculty_id') == $f->faculty_id ? 'selected' : '' }}>{{ $f->faculty_name }}</option>
                                    @endforeach
                                </select>
                                <div id="departmentWrapper" style="{{ request('faculty_id') ? '' : 'display: none;' }}">
                                    <select name="dept_id" id="departmentSelect" class="form-select form-select-sm border-slate-200 rounded-lg text-[11px] font-bold">
                                        <option value="">-- Dept --</option>
                                        @foreach($departments as $d)
                                            <option value="{{ $d->dept_id }}" data-parent="{{ $d->faculty_id }}" {{ request('dept_id') == $d->dept_id ? 'selected' : '' }}>{{ $d->dept_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 h-full shadow-sm">
                                <h6 class="text-[9px] font-black text-sky-600 uppercase tracking-widest mb-2 italic">Admin Branch</h6>
                                <select name="office_id" id="officeSelect" class="form-select form-select-sm border-slate-200 rounded-lg text-[11px] font-bold mb-2">
                                    <option value="">-- Office --</option>
                                    @foreach($offices as $o)
                                        <option value="{{ $o->office_id }}" {{ request('office_id') == $o->office_id ? 'selected' : '' }}>{{ $o->office_name }}</option>
                                    @endforeach
                                </select>
                                <div id="unitWrapper" style="{{ request('office_id') ? '' : 'display: none;' }}">
                                    <select name="unit_id" id="unitSelect" class="form-select form-select-sm border-slate-200 rounded-lg text-[11px] font-bold">
                                        <option value="">-- Unit --</option>
                                        @foreach($units as $u)
                                            <option value="{{ $u->unit_id }}" data-parent="{{ $u->office_id }}" {{ request('unit_id') == $u->unit_id ? 'selected' : '' }}>{{ $u->unit_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 h-full shadow-sm">
                                <h6 class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-2 italic">Independent Bodies</h6>
                                <select name="institute_id" class="form-select form-select-sm border-slate-200 rounded-lg text-[11px] font-bold mb-2">
                                    <option value="">-- Institute --</option>
                                    @foreach($institutes as $i)
                                        <option value="{{ $i->institute_id }}" {{ request('institute_id') == $i->institute_id ? 'selected' : '' }}>{{ $i->institute_name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[8px] text-slate-400 font-bold uppercase italic mb-0 tracking-widest">Research / Centers</p>
                            </div>
                        </div>

                        <div class="col-12 col-md-6 col-lg-3">
                            <div class="p-3 rounded-2xl bg-slate-900 border border-slate-800 h-full shadow-lg">
                                <h6 class="text-[9px] font-black text-indigo-300 uppercase tracking-widest mb-2 italic">Classification</h6>
                                <select name="category_id" id="categorySelect" class="form-select form-select-sm border-0 rounded-lg text-[11px] font-bold mb-2 bg-slate-800 text-white focus:ring-indigo-500">
                                    <option value="" class="text-slate-800">-- Category --</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->category_id }}" {{ request('category_id') == $c->category_id ? 'selected' : '' }}>{{ $c->category_name }}</option>
                                    @endforeach
                                </select>
                                <div id="subcategoryWrapper" style="{{ request('category_id') ? '' : 'display: none;' }}">
                                    <select name="sub_id" id="subcategorySelect" class="form-select form-select-sm border-0 rounded-lg text-[11px] font-bold bg-slate-800 text-white">
                                        <option value="" class="text-slate-800">-- Subcategory --</option>
                                        @foreach($subcategories as $s)
                                            <option value="{{ $s->subcategory_id }}" data-parent="{{ $s->category_id }}" {{ request('sub_id') == $s->subcategory_id ? 'selected' : '' }}>{{ $s->subcategory_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 flex flex-col md:flex-row items-center justify-end gap-3 mt-2 border-t border-slate-100 pt-4">
                            <a href="{{ route('auditor.registry.index') }}" class="order-2 md:order-1 text-[10px] font-black text-slate-400 hover:text-rose-500 uppercase tracking-widest no-underline">RESET</a>
                            <button type="submit" class="order-1 md:order-2 w-full md:w-auto bg-indigo-600 text-white px-10 py-2.5 rounded-full font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 shadow-md transition-all">
                                APPLY FILTERS
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="px-3 px-md-4 mb-3">
        <ul class="nav nav-pills gap-2" id="registryTab" role="tablist">
            @foreach(['pending' => 'Pending', 'rejected' => 'Rejected', 'approved' => 'Approved', 'all' => 'All History'] as $key => $label)
                <li class="nav-item">
                    <button class="nav-link {{ $key == 'pending' ? 'active' : '' }} flex items-center gap-2 rounded-xl px-4 py-2 fw-black text-[10px] md:text-xs uppercase tracking-widest border border-slate-100 shadow-sm" 
                            data-bs-toggle="tab" data-bs-target="#{{ $key }}">
                        {{ $label }}
                        <span class="badge {{ $key == 'rejected' ? 'bg-rose-500' : ($key == 'approved' ? 'bg-emerald-500' : 'bg-slate-900') }} rounded-md text-[9px]">
                            {{ $statusCounts[$key] }}
                        </span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="mx-3 mx-md-4 overflow-hidden rounded-[1.5rem] md:rounded-[2.5rem] bg-white border border-slate-100 shadow-xl shadow-slate-200/50">
        <div class="tab-content">
            @foreach($tabbed_data as $key => $tabInfo)
            <div class="tab-pane fade {{ $key == 'pending' ? 'show active' : '' }}" id="{{ $key }}">
                <div class="hidden md:block overflow-x-auto">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="{{ $tabInfo['theme'] }} text-white">
                                <th class="ps-5 py-4 fw-black uppercase text-[10px] tracking-widest border-0">Sub ID</th>
                                <th class="py-4 fw-black uppercase text-[10px] tracking-widest border-0">Entity / Department</th>
                                <th class="py-4 fw-black uppercase text-[10px] tracking-widest border-0">Item Specification</th>
                                <th class="py-4 fw-black uppercase text-[10px] tracking-widest border-0 text-center">Qty</th>
                                <th class="py-4 fw-black uppercase text-[10px] tracking-widest border-0">Funding</th>
                                <th class="py-4 fw-black uppercase text-[10px] tracking-widest border-0">Total Value</th>
                                <th class="pe-5 py-4 fw-black uppercase text-[10px] tracking-widest border-0 text-end">Manage</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            @forelse($tabInfo['data'] as $item)
                                <tr class="group border-bottom border-slate-50 transition-colors hover:bg-slate-50/50">
                                    <td class="ps-5 py-4 font-mono fw-black text-indigo-600">
                                        #{{ str_pad($item->submission_id, 4, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td>
                                        <div class="fw-black text-slate-800 text-[11px] uppercase">{{ $item->source_name }}</div>
                                        <div class="text-[9px] fw-bold text-slate-400 uppercase tracking-tighter">{{ $item->parent_branch_name }}</div>
                                    </td>
                                    <td>
                                        <div class="text-slate-700 font-black text-[11px] uppercase truncate max-w-[220px]">{{ $item->item_name }}</div>
                                        <div class="text-[9px] text-slate-400 font-medium uppercase italic">Serial: {{ $item->serial_number ?? 'N/A' }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded fw-black text-[10px]">
                                            {{ $item->quantity }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="text-[9px] fw-black text-slate-500 uppercase">{{ $item->funding_source_per_item ?? 'General' }}</span>
                                    </td>
                                    <td>
                                        <span class="text-slate-900 fw-black text-[11px]">
                                            ₦{{ number_format($item->total_value, 2) }}
                                        </span>
                                    </td>
                                    <td class="pe-5 text-end">
                                        <a href="{{ $item->action_route }}" 
                                           class="inline-flex items-center px-4 py-2 rounded-xl bg-white border-2 {{ $item->action_class }} fw-black text-[10px] uppercase no-underline hover:text-white transition-all">
                                            {{ $item->action_label }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="py-20 text-center text-slate-300 fw-black uppercase tracking-widest italic">No records found matching your filters</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile View --}}
                <div class="md:hidden divide-y divide-slate-100">
                    @forelse($tabInfo['data'] as $item)
                        <div class="p-5 bg-white">
                            <div class="flex justify-between items-start mb-3">
                                <span class="font-black text-indigo-600 text-base">#{{ str_pad($item->submission_id, 4, '0', STR_PAD_LEFT) }}</span>
                                <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded font-black text-[9px] uppercase">Qty: {{ $item->quantity }}</span>
                            </div>
                            <h3 class="text-sm font-black text-slate-800 uppercase mb-1">{{ $item->item_name }}</h3>
                            <div class="text-[10px] text-slate-400 font-bold uppercase mb-3">{{ $item->source_name }}</div>
                            <div class="flex justify-between items-center bg-slate-50 p-3 rounded-xl mb-4">
                                <div class="text-[10px] font-black text-slate-500 uppercase">Value</div>
                                <div class="text-sm font-black text-slate-900">₦{{ number_format($item->total_value, 2) }}</div>
                            </div>
                            <a href="{{ $item->action_route }}" 
                               class="block text-center {{ $item->action_btn_bg }} text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-md transition-all">
                                {{ $item->action_label }}
                            </a>
                        </div>
                    @empty
                        <div class="p-10 text-center text-slate-400 font-black uppercase text-xs">No records found</div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    /**
     * Helper function to filter dependent dropdowns
     * @param {string} parentId - ID of the parent select (e.g., facultySelect)
     * @param {string} childId  - ID of the child select (e.g., departmentSelect)
     * @param {string} wrapperId - ID of the div containing the child
     */
    const setupDependentDropdown = (parentId, childId, wrapperId) => {
        const parentSelect = document.getElementById(parentId);
        const childSelect = document.getElementById(childId);
        const wrapper = document.getElementById(wrapperId);

        if (!parentSelect || !childSelect || !wrapper) return;

        parentSelect.addEventListener('change', function () {
            const selectedParentId = this.value;
            const childOptions = childSelect.querySelectorAll('option');

            // Reset child selection
            childSelect.value = "";

            let hasVisibleOptions = false;

            childOptions.forEach(option => {
                const parentDataId = option.getAttribute('data-parent');
                
                // Always show the default placeholder
                if (option.value === "") {
                    option.style.display = 'block';
                } 
                // Show if it matches parent, or if no parent is selected (optional)
                else if (parentDataId === selectedParentId) {
                    option.style.display = 'block';
                    hasVisibleOptions = true;
                } else {
                    option.style.display = 'none';
                }
            });

            // Show wrapper only if a parent is selected
            wrapper.style.display = selectedParentId ? 'block' : 'none';
        });
    };

    // Initialize for Academic Branch
    setupDependentDropdown('facultySelect', 'departmentSelect', 'departmentWrapper');
    
    // Initialize for Admin Branch
    setupDependentDropdown('officeSelect', 'unitSelect', 'unitWrapper');
    
    // Initialize for Classification (Categories -> Subcategories)
    setupDependentDropdown('categorySelect', 'subcategorySelect', 'subcategoryWrapper');
});
</script>
@endpush
@endsection