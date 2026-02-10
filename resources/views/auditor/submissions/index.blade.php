@extends('layouts.auditor')

@section('title', 'Pending Verifications - Auditor Dashboard')

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    {{-- Standardized container to prevent edge-to-edge stretching on desktop --}}
    <div class="container mx-auto px-4 max-w-7xl">
        
        {{-- 1. HEADER & ACTION BAR --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
            <div class="flex items-center gap-4">
                {{-- RESTORED BACK BUTTON --}}
                <a href="{{ url()->previous() }}" 
                   class="flex items-center justify-center w-11 h-11 rounded-xl border border-slate-200 bg-white text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition-all shrink-0 group shadow-sm">
                    <i class="fas fa-chevron-left text-sm group-hover:-translate-x-0.5 transition-transform"></i>
                </a>

                <div class="min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-indigo-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-indigo-600"></span>
                        </span>
                        <h6 class="text-[10px] font-black uppercase tracking-widest text-slate-500 truncate">
                            College of Medicine Inventory
                        </h6>
                    </div>
                    <h1 class="text-xl md:text-3xl font-black text-slate-900 tracking-tight truncate">
                        Pending Verifications
                    </h1>
                </div>
            </div>
            
            {{-- ANIMATED LIVE QUEUE CARD (The whole div animates) --}}
            <div class="animate-pulse bg-white border-2 border-indigo-100 rounded-2xl p-2 shadow-md flex items-center self-start lg:self-center shrink-0">
                <div class="relative flex items-center justify-center p-2">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white relative z-10 shadow-lg">
                        <span class="text-sm font-black">{{ $submissions->total() }}</span>
                    </div>
                </div>
                <div class="ml-3 pr-4">
                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Live Queue</div>
                    <div class="text-xs font-black text-indigo-600 uppercase">
                        {{ $submissions->total() }} Records 
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. SEARCH & HIERARCHY FILTERS --}}
        <div class="mb-8">
            <div class="bg-white rounded-[2rem] shadow-sm border-t-4 border-indigo-600 overflow-hidden">
                <div class="p-6">
                    <form action="{{ route('auditor.submissions.index') }}" method="GET" id="filterForm">
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                            
                            {{-- Search Bar --}}
                            <div class="col-span-12">
                                <div class="flex items-center bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                                    <span class="pl-4 pr-2 text-slate-400"><i class="fas fa-search"></i></span>
                                    <input type="text" name="search" 
                                        class="w-full border-0 bg-transparent py-3.5 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:ring-0" 
                                        placeholder="Search Reference, Submitter, or Item..." 
                                        value="{{ request('search') }}">
                                    @if(request('search'))
                                        <a href="{{ route('auditor.submissions.index') }}" class="px-3 text-slate-300 hover:text-rose-500 transition-colors">
                                            <i class="fas fa-times-circle text-lg"></i>
                                        </a>
                                    @endif
                                    <button type="submit" class="bg-slate-900 text-white px-8 py-3.5 font-black text-xs uppercase tracking-widest hover:bg-indigo-600 transition-colors">
                                        SEARCH
                                    </button>
                                </div>
                            </div>

                            {{-- Academic Branch --}}
                            <div class="col-span-12 lg:col-span-3">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-white h-full">
                                    <h6 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-3 italic">Academic Branch</h6>
                                    <div class="flex flex-col gap-2">
                                        <select name="faculty_id" id="facultySelect" class="w-full text-[11px] font-bold bg-white border-slate-200 rounded-xl py-2 focus:ring-indigo-500/10">
                                            <option value="">-- Faculty --</option>
                                            @foreach($faculties as $f)
                                                <option value="{{ $f->faculty_id }}" {{ request('faculty_id') == $f->faculty_id ? 'selected' : '' }}>{{ $f->faculty_name }}</option>
                                            @endforeach
                                        </select>
                                        <div id="departmentWrapper" class="{{ request('faculty_id') ? '' : 'hidden' }}">
                                            <select name="dept_id" id="departmentSelect" class="w-full text-[11px] font-bold bg-white border-slate-200 rounded-xl py-2 focus:ring-indigo-500/10">
                                                <option value="">-- Dept --</option>
                                                @foreach($departments as $d)
                                                    <option value="{{ $d->dept_id }}" data-parent="{{ $d->faculty_id }}" {{ request('dept_id') == $d->dept_id ? 'selected' : '' }}>{{ $d->dept_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Admin Branch --}}
                            <div class="col-span-12 lg:col-span-3">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-white h-full">
                                    <h6 class="text-[10px] font-black text-sky-600 uppercase tracking-widest mb-3 italic">Admin Branch</h6>
                                    <div class="flex flex-col gap-2">
                                        <select name="office_id" id="officeSelect" class="w-full text-[11px] font-bold bg-white border-slate-200 rounded-xl py-2 focus:ring-indigo-500/10">
                                            <option value="">-- Office --</option>
                                            @foreach($offices as $o)
                                                <option value="{{ $o->office_id }}" {{ request('office_id') == $o->office_id ? 'selected' : '' }}>{{ $o->office_name }}</option>
                                            @endforeach
                                        </select>
                                        <div id="unitWrapper" class="{{ request('office_id') ? '' : 'hidden' }}">
                                            <select name="unit_id" id="unitSelect" class="w-full text-[11px] font-bold bg-white border-slate-200 rounded-xl py-2 focus:ring-indigo-500/10">
                                                <option value="">-- Unit --</option>
                                                @foreach($units as $u)
                                                    <option value="{{ $u->unit_id }}" data-parent="{{ $u->office_id }}" {{ request('unit_id') == $u->unit_id ? 'selected' : '' }}>{{ $u->unit_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Institute Branch --}}
                            <div class="col-span-12 lg:col-span-3">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-white h-full">
                                    <h6 class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-3 italic">Research Units</h6>
                                    <select name="institute_id" class="w-full text-[11px] font-bold bg-white border-slate-200 rounded-xl py-2 focus:ring-indigo-500/10">
                                        <option value="">-- Select Institute --</option>
                                        @foreach($institutes as $i)
                                            <option value="{{ $i->institute_id }}" {{ request('institute_id') == $i->institute_id ? 'selected' : '' }}>{{ $i->institute_name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-[9px] text-slate-400 font-bold mt-2 uppercase tracking-tighter italic">Independent Institutes</p>
                                </div>
                            </div>

                           <div class="col-span-12 lg:col-span-3">
                                <div class="p-4 rounded-2xl bg-indigo-900 border border-indigo-800 h-full shadow-lg transition-all duration-300">
                                    <h6 class="text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-3 italic">Asset Classification</h6>
                                    <div class="flex flex-col gap-2">
                                        
                                        {{-- Main Category --}}
                                        <select name="category_id" id="categorySelect" class="w-full text-[11px] font-bold bg-white border-0 rounded-xl py-2 focus:ring-2 focus:ring-indigo-400 cursor-pointer">
                                            <option value="">-- Select Category --</option>
                                            @foreach($categories as $c)
                                                <option value="{{ $c->category_id }}" {{ request('category_id') == $c->category_id ? 'selected' : '' }}>
                                                    {{ $c->category_name }}
                                                </option>
                                            @endforeach
                                        </select>

                                        {{-- Subcategory Wrapper with Fade-In Effect --}}
                                        <div id="subcategoryWrapper" class="transition-all duration-500 {{ request('category_id') ? 'opacity-100 scale-100' : 'hidden opacity-0 scale-95' }}">
                                            <select name="sub_id" id="subcategorySelect" class="w-full text-[11px] font-bold bg-white border-0 rounded-xl py-2 focus:ring-2 focus:ring-indigo-400 shadow-inner">
                                                <option value="">-- Choose Subcategory --</option>
                                                @foreach($subcategories as $s)
                                                    <option value="{{ $s->subcategory_id }}" 
                                                            data-parent="{{ $s->category_id }}" 
                                                            {{ request('sub_id') == $s->subcategory_id ? 'selected' : '' }}>
                                                        {{ $s->subcategory_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="col-span-12 flex items-center justify-end gap-4 mt-2 border-t border-slate-100 pt-4">
                                <a href="{{ route('auditor.submissions.index') }}" class="text-[10px] font-black text-slate-400 hover:text-rose-500 uppercase tracking-widest transition-colors no-underline">
                                    <i class="fas fa-undo-alt me-1"></i> RESET ALL
                                </a>
                                <button type="submit" class="bg-indigo-600 text-white px-10 py-2.5 rounded-full font-black text-[10px] uppercase tracking-widest hover:bg-indigo-700 transition-all shadow-md shadow-indigo-200">
                                    APPLY SEARCH PARAMETERS
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. DATA TABLE --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th class="ps-8 py-4 text-[10px] font-black uppercase tracking-widest">Ref Number</th>
                            <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Item</th>
                            <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Item Location</th>
                            <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Submitted By</th>
                            <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">Qty</th>
                            <th class="pe-8 py-4 text-[10px] font-black uppercase tracking-widest text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($submissions as $submission)
                            @php $u = $submission->submittedBy; @endphp
                            <tr class="hover:bg-indigo-50/50 transition-colors group">
                                <td class="ps-8 py-5 align-middle font-black text-indigo-600 text-sm italic">#{{ str_pad($submission->submission_id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-4 py-5 align-middle">
                                    <div class="text-[11px] font-black text-slate-800 uppercase leading-none">
                                        {{ $submission->items->first()->item_name ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-4 py-5 align-middle">
                                    <div class="flex flex-col">
                                        @if($u->faculty) 
                                            <span class="text-[11px] font-black text-slate-900 uppercase">{{ $u->faculty->faculty_name }}</span>
                                            <span class="text-[10px] text-slate-400 font-bold italic">{{ $u->department->dept_name ?? 'General' }}</span>
                                        @elseif($u->office)
                                            <span class="text-[11px] font-black text-slate-900 uppercase">{{ $u->office->office_name }}</span>
                                            <span class="text-[10px] text-slate-400 font-bold italic">{{ $u->unit->unit_name ?? 'General' }}</span>
                                        @elseif($u->institute)
                                            <span class="text-[11px] font-black text-slate-900 uppercase">{{ $u->institute->institute_name }}</span>
                                        @else
                                            <span class="text-[11px] font-bold text-slate-400 italic">College Admin</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-5 align-middle">
                                    <div class="text-[11px] font-black text-slate-800 leading-none mb-1">{{ $u->profile->full_name ?? $u->username }}</div>
                                    <div class="text-[9px] text-slate-400 font-medium lowercase">{{ $u->email }}</div>
                                </td>
                                <td class="px-4 py-5 align-middle text-center">
                                    <span class="bg-slate-100 text-slate-600 px-3 py-1 rounded-lg font-black text-[10px]">{{ $submission->items->count() }}</span>
                                </td>
                                <td class="pe-8 py-5 align-middle text-end">
                                    <a href="{{ route('auditor.submissions.show', $submission->submission_id) }}" 
                                       class="bg-slate-900 text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all shadow-sm">
                                        Audit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-20 text-center font-black text-slate-300 uppercase text-xs">No Pending Verifications</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE VIEW --}}
            <div class="md:hidden divide-y divide-slate-100">
                @foreach($submissions as $submission)
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-3">
                            <span class="font-black text-indigo-600">#{{ $submission->submission_id }}</span>
                            <span class="bg-indigo-50 text-indigo-600 px-2 py-1 rounded font-black text-[9px] uppercase">
                                {{ $submission->items->count() }} Items
                            </span>
                        </div>
                        <h3 class="text-xs font-black text-slate-800 uppercase mb-2">{{ $submission->items->first()->item_name }}</h3>
                        <div class="text-[10px] text-slate-500 font-bold italic mb-4">
                            @php $u = $submission->submittedBy; @endphp
                            @if($u->faculty) {{ $u->faculty->faculty_name }} / {{ $u->department->dept_name ?? '' }}
                            @elseif($u->office) {{ $u->office->office_name }} / {{ $u->unit->unit_name ?? '' }}
                            @elseif($u->institute) {{ $u->institute->institute_name }}
                            @endif
                        </div>
                        <a href="{{ route('auditor.submissions.show', $submission->submission_id) }}" 
                           class="block text-center bg-slate-900 text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-md">
                            Review Submission
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8">
            {{ $submissions->links() }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. SELECTORS
    const facultySelect = document.getElementById('facultySelect');
    const departmentWrapper = document.getElementById('departmentWrapper');
    const departmentSelect = document.getElementById('departmentSelect');
    const deptOptions = Array.from(departmentSelect.options);

    const officeSelect = document.getElementById('officeSelect');
    const unitWrapper = document.getElementById('unitWrapper');
    const unitSelect = document.getElementById('unitSelect');
    const unitOptions = Array.from(unitSelect.options);

    // Added: Asset Classification Selectors
    const categorySelect = document.getElementById('categorySelect');
    const subcategoryWrapper = document.getElementById('subcategoryWrapper');
    const subcategorySelect = document.getElementById('subcategorySelect');
    const subOptions = Array.from(subcategorySelect.options);

    // 2. EVENT LISTENERS
    facultySelect.addEventListener('change', function () {
        const selectedId = this.value;
        departmentWrapper.classList.toggle('hidden', !selectedId);
        filterDropdown(departmentSelect, deptOptions, 'data-parent', selectedId);
    });

    officeSelect.addEventListener('change', function () {
        const selectedId = this.value;
        unitWrapper.classList.toggle('hidden', !selectedId);
        filterDropdown(unitSelect, unitOptions, 'data-parent', selectedId);
    });

    // Added: Category Change Listener
    categorySelect.addEventListener('change', function () {
        const selectedId = this.value;
        
        if (selectedId) {
            // Show the wrapper with animation classes
            subcategoryWrapper.classList.remove('hidden');
            setTimeout(() => {
                subcategoryWrapper.classList.remove('opacity-0', 'scale-95');
                subcategoryWrapper.classList.add('opacity-100', 'scale-100');
            }, 10);
        } else {
            // Hide the wrapper
            subcategoryWrapper.classList.add('hidden', 'opacity-0', 'scale-95');
            subcategorySelect.value = '';
        }
        
        filterDropdown(subcategorySelect, subOptions, 'data-parent', selectedId);
    });

    // 3. UTILITY FILTER FUNCTION
    function filterDropdown(selectElement, allOptions, dataAttr, parentId) {
        selectElement.innerHTML = '';
        // Re-add the placeholder (e.g., "-- Choose Subcategory --")
        selectElement.appendChild(allOptions[0]);
        
        allOptions.forEach(option => {
            if (option.getAttribute(dataAttr) === parentId) {
                selectElement.appendChild(option);
            }
        });
    }

    // 4. INITIAL STATE (On Page Load/Refresh)
    if(facultySelect.value) facultySelect.dispatchEvent(new Event('change'));
    if(officeSelect.value) officeSelect.dispatchEvent(new Event('change'));
    
    // Trigger category change if one is already selected (from a previous search)
    if(categorySelect.value) {
        // Run the filter immediately
        filterDropdown(subcategorySelect, subOptions, 'data-parent', categorySelect.value);
        // Ensure the correct subcategory is selected after the filter runs
        subcategorySelect.value = "{{ request('sub_id') }}";
    }
});
</script>
@endsection