@extends('layouts.auditor')

@section('title', 'Verified Registry')

@section('content')
<div class="min-h-screen bg-slate-50 py-8">
    <div class="container mx-auto px-4 max-w-7xl">
        
        {{-- 1. HEADER & ACTION BAR --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() }}" 
                   class="flex items-center justify-center w-11 h-11 rounded-xl border border-slate-200 bg-white text-slate-400 hover:text-indigo-600 hover:border-indigo-100 transition-all shrink-0 group shadow-sm">
                    <i class="fas fa-chevron-left text-sm group-hover:-translate-x-0.5 transition-transform"></i>
                </a>

                <div class="min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
                        </span>
                        <h6 class="text-[10px] font-black uppercase tracking-widest text-slate-500 truncate">
                            College of Medicine Inventory
                        </h6>
                    </div>
                    <h1 class="text-xl md:text-3xl font-black text-slate-900 tracking-tight truncate">
                        Verified Registry
                    </h1>
                </div>
            </div>
            
            <a href="{{ route('auditor.reports.export', request()->all()) }}" 
               class="bg-white border-2 border-emerald-100 rounded-2xl p-3 px-6 shadow-sm flex items-center self-start lg:self-center shrink-0 hover:bg-emerald-50 transition-all group">
                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform">
                    <i class="fas fa-file-csv"></i>
                </div>
                <div class="ml-3">
                    <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Reports</div>
                    <div class="text-xs font-black text-emerald-600 uppercase">Export CSV Data</div>
                </div>
            </a>
        </div>

        {{-- 2. SEARCH & HIERARCHY FILTERS --}}
        {{-- 2. SEARCH & HIERARCHY FILTERS --}}
        <div class="mb-8">
            <div class="bg-white rounded-[2rem] shadow-sm border-t-4 border-indigo-600 overflow-hidden">
                <div class="p-6">
                    <form action="{{ route('auditor.approved_items.index') }}" method="GET" id="filterForm">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                            
                            {{-- Full Width Search --}}
                            <div class="md:col-span-12">
                                <div class="flex items-center bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                                    <span class="pl-4 pr-2 text-slate-400"><i class="fas fa-search"></i></span>
                                    <input type="text" name="search" 
                                        class="w-full border-0 bg-transparent py-3.5 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:ring-0" 
                                        placeholder="Search Reference, Item Name, or Tag Number..." 
                                        value="{{ request('search') }}">
                                </div>
                            </div>

                            {{-- Academic Branch --}}
                            <div class="md:col-span-4">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 h-full">
                                    <h6 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-3 italic">Academic Branch</h6>
                                    <div class="flex flex-col gap-2">
                                        <select name="faculty_id" id="facultySelect" class="w-full text-[11px] font-bold bg-white border-slate-200 rounded-xl py-2">
                                            <option value="">-- Select Faculty --</option>
                                            @foreach($faculties as $f)
                                                <option value="{{ $f->faculty_id }}" {{ request('faculty_id') == $f->faculty_id ? 'selected' : '' }}>{{ $f->faculty_name }}</option>
                                            @endforeach
                                        </select>
                                        <div id="departmentWrapper" class="{{ request('faculty_id') ? '' : 'hidden' }}">
                                            <select name="dept_id" id="departmentSelect" class="w-full text-[11px] font-bold bg-white border-slate-200 rounded-xl py-2">
                                                <option value="">-- Select Dept --</option>
                                                @foreach($departments as $d)
                                                    <option value="{{ $d->dept_id }}" data-parent="{{ $d->faculty_id }}" {{ request('dept_id') == $d->dept_id ? 'selected' : '' }}>{{ $d->dept_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Admin Branch --}}
                            <div class="md:col-span-4">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 h-full">
                                    <h6 class="text-[10px] font-black text-sky-600 uppercase tracking-widest mb-3 italic">Administrative Offices</h6>
                                    <div class="flex flex-col gap-2">
                                        <select name="office_id" id="officeSelect" class="w-full text-[11px] font-bold bg-white border-slate-200 rounded-xl py-2">
                                            <option value="">-- Select Office --</option>
                                            @foreach($offices as $o)
                                                <option value="{{ $o->office_id }}" {{ request('office_id') == $o->office_id ? 'selected' : '' }}>{{ $o->office_name }}</option>
                                            @endforeach
                                        </select>
                                        <div id="unitWrapper" class="{{ request('office_id') ? '' : 'hidden' }}">
                                            <select name="unit_id" id="unitSelect" class="w-full text-[11px] font-bold bg-white border-slate-200 rounded-xl py-2">
                                                <option value="">-- Select Unit --</option>
                                                @foreach($units as $u)
                                                    <option value="{{ $u->unit_id }}" data-parent="{{ $u->office_id }}" {{ request('unit_id') == $u->unit_id ? 'selected' : '' }}>{{ $u->unit_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Institute Branch --}}
                            <div class="md:col-span-4">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 h-full">
                                    <h6 class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-3 italic">Independent Institutes</h6>
                                    <select name="institute_id" class="w-full text-[11px] font-bold bg-white border-slate-200 rounded-xl py-2">
                                        <option value="">-- Select Institute --</option>
                                        @foreach($institutes as $i)
                                            <option value="{{ $i->institute_id }}" {{ request('institute_id') == $i->institute_id ? 'selected' : '' }}>{{ $i->institute_name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="text-[8px] text-slate-400 font-bold mt-3 uppercase italic">Research & Specialist Centers</p>
                                </div>
                            </div>

                            {{-- Audit Date Range (Wider) --}}
                            <div class="md:col-span-7">
                                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 h-full shadow-lg">
                                    <h6 class="text-[10px] font-black text-indigo-300 uppercase tracking-widest mb-3 italic">Verification Date Filter</h6>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="relative">
                                            <label class="absolute -top-2 left-3 bg-slate-900 px-2 text-[8px] font-black text-indigo-400 uppercase">Start Date</label>
                                            <input type="date" name="date_from" value="{{ request('date_from') }}" 
                                                class="w-full text-xs font-bold bg-transparent border border-slate-700 text-white rounded-xl py-2.5 focus:ring-indigo-500">
                                        </div>
                                        <div class="relative">
                                            <label class="absolute -top-2 left-3 bg-slate-900 px-2 text-[8px] font-black text-indigo-400 uppercase">End Date</label>
                                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                                class="w-full text-xs font-bold bg-transparent border border-slate-700 text-white rounded-xl py-2.5 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Asset Category --}}
                            <div class="md:col-span-5">
                                <div class="p-4 rounded-2xl bg-indigo-50 border border-indigo-100 h-full">
                                    <h6 class="text-[10px] font-black text-indigo-700 uppercase tracking-widest mb-3 italic">Asset Classification</h6>
                                    <select name="category_id" class="w-full text-[11px] font-bold bg-white border-indigo-200 rounded-xl py-2.5">
                                        <option value="">All Categories</option>
                                        @foreach($categories as $c)
                                            <option value="{{ $c->category_id }}" {{ request('category_id') == $c->category_id ? 'selected' : '' }}>{{ $c->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="md:col-span-12 flex items-center justify-end gap-4 border-t border-slate-100 pt-6">
                                <a href="{{ route('auditor.approved_items.index') }}" class="text-[10px] font-black text-slate-400 hover:text-rose-500 uppercase tracking-widest transition-colors no-underline">
                                    <i class="fas fa-sync-alt me-1"></i> Clear Filters
                                </a>
                                <button type="submit" class="bg-indigo-600 text-white px-12 py-3 rounded-full font-black text-[10px] uppercase tracking-widest hover:bg-slate-900 transition-all shadow-xl">
                                    Filter Verified Records
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 3. DATA TABLE & MOBILE CARDS --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
            {{-- Stats Bar --}}
            <div class="bg-slate-50 border-b border-slate-100 px-6 md:px-8 py-4 flex flex-wrap gap-4 md:gap-8">
                <div>
                    <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Registry Valuation</span>
                    <span class="text-sm md:text-lg font-black text-slate-900">₦{{ number_format($submissions->sum(fn($s) => $s->items->sum(fn($i) => ($i->cost ?? 0) * $i->quantity)), 2) }}</span>
                </div>
                <div>
                    <span class="block text-[9px] font-black text-slate-400 uppercase tracking-widest">Total Items</span>
                    <span class="text-sm md:text-lg font-black text-indigo-600">{{ $submissions->sum(fn($s) => $s->items->count()) }} Units</span>
                </div>
            </div>

            {{-- MOBILE VIEW: Visible on small screens, hidden on desktop --}}
            <div class="block md:hidden divide-y divide-slate-100">
                @forelse($submissions as $submission)
                    @php 
                        $items = $submission->items;
                        $u = $submission->submittedBy;
                    @endphp
                    <div class="p-5 flex flex-col gap-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <span class="text-xs font-black text-indigo-600 italic">#{{ str_pad($submission->submission_id, 4, '0', STR_PAD_LEFT) }}</span>
                                <h3 class="text-[11px] font-black text-slate-800 uppercase mt-1 leading-tight">
                                    {{ $items->pluck('item_name')->implode(', ') }}
                                </h3>
                            </div>
                            <div class="bg-slate-900 text-white px-3 py-1 rounded-lg text-[10px] font-black">
                                ₦{{ number_format($items->sum(fn($i) => ($i->cost ?? 0) * $i->quantity), 0) }}
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="block text-[8px] font-black text-slate-400 uppercase">Location</span>
                                <p class="text-[10px] font-bold text-slate-700 truncate">
                                    @if($u->faculty) {{ $u->faculty->faculty_name }}
                                    @elseif($u->office) {{ $u->office->office_name }}
                                    @else {{ $u->institute->institute_name ?? 'N/A' }} @endif
                                </p>
                            </div>
                            <div>
                                <span class="block text-[8px] font-black text-slate-400 uppercase">Outcome</span>
                                <div class="flex gap-2">
                                    <span class="text-[10px] font-black text-emerald-600">{{ $items->where('status', 'approved')->count() }}✓</span>
                                    <span class="text-[10px] font-black text-rose-500">{{ $items->where('status', 'rejected')->count() }}✗</span>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('auditor.approved_items.show', $submission->submission_id) }}" 
                        class="w-full bg-slate-100 text-slate-900 py-2.5 rounded-xl text-center text-[10px] font-black uppercase tracking-widest hover:bg-indigo-600 hover:text-white transition-all">
                            View Full Details
                        </a>
                    </div>
                @empty
                    <div class="p-10 text-center font-black text-slate-300 uppercase text-xs">No entries found</div>
                @endforelse
            </div>

            {{-- DESKTOP VIEW: Hidden on mobile, visible on medium screens and up --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left table-fixed min-w-[1000px]">
                    <thead>
                        <tr class="bg-slate-900 text-white">
                            <th class="w-20 ps-8 py-4 text-[10px] font-black uppercase tracking-widest">Ref ID</th>
                            <th class="w-48 px-4 py-4 text-[10px] font-black uppercase tracking-widest">Item Description</th>
                            <th class="w-40 px-4 py-4 text-[10px] font-black uppercase tracking-widest">Origin</th>
                            <th class="w-40 px-4 py-4 text-[10px] font-black uppercase tracking-widest">Submitted By</th>
                            <th class="w-32 px-4 py-4 text-[10px] font-black uppercase tracking-widest">Auditor</th>
                            <th class="w-24 px-4 py-4 text-[10px] font-black uppercase tracking-widest text-center">Outcome</th>
                            <th class="w-24 pe-8 py-4 text-[10px] font-black uppercase tracking-widest text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($submissions as $submission)
                            @php 
                                $items = $submission->items;
                                $u = $submission->submittedBy;
                                $auditor = $submission->reviewedBy;
                            @endphp
                            <tr class="hover:bg-indigo-50/50 transition-colors group">
                                <td class="ps-8 py-5 font-black text-indigo-600 text-xs italic">
                                    #{{ str_pad($submission->submission_id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-4 py-5">
                                    <div class="text-[11px] font-black text-slate-800 uppercase truncate" 
                                        title="{{ $items->pluck('item_name')->implode(', ') }}">
                                        {{ $items->pluck('item_name')->implode(', ') }}
                                    </div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <div class="text-[9px] text-slate-400 font-bold italic">
                                            Total: ₦{{ number_format($items->sum(fn($i) => ($i->cost ?? 0) * $i->quantity), 2) }}
                                        </div>
                                        @if($items->count() > 1)
                                            <span class="bg-indigo-100 text-indigo-600 text-[8px] px-1.5 py-0.5 rounded-md font-black">
                                                {{ $items->count() }} ITEMS
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-5">
                                    <div class="flex flex-col truncate">
                                        @if($u->faculty)
                                            <span class="text-[10px] font-black text-slate-700 uppercase truncate">{{ $u->faculty->faculty_name }}</span>
                                            <span class="text-[9px] text-slate-600 font-bold italic truncate">{{ $u->department->dept_name ?? 'General' }}</span>
                                        @elseif($u->office)
                                            <span class="text-[10px] font-black text-slate-700 uppercase truncate">{{ $u->office->office_name }}</span>
                                            <span class="text-[9px] text-slate-600 font-bold italic truncate">{{ $u->unit->unit_name ?? 'General' }}</span>
                                        @elseif($u->institute)
                                            <span class="text-[10px] font-black text-slate-700 uppercase truncate">{{ $u->institute->institute_name }}</span>
                                            <span class="text-[9px] text-slate-600 font-bold italic">Institute</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-5">
                                    <div class="text-[10px] font-black text-slate-800 truncate">{{ $u->profile->full_name ?? $u->username }}</div>
                                    <div class="text-[9px] text-slate-700 font-medium truncate italic">{{ $u->email }}</div>
                                </td>
                                <td class="px-4 py-5">
                                    <div class="text-[10px] font-black text-slate-700 uppercase truncate">
                                        {{ $auditor->username ?? 'System' }}
                                    </div>
                                    <span class="text-[8px] text-emerald-500 font-black uppercase tracking-tighter">Verified</span>
                                </td>
                                <td class="px-4 py-5 text-center">
                                    <div class="inline-flex items-center bg-slate-50 border border-slate-200 rounded-lg px-2 py-0.5 gap-1.5">
                                        <span class="text-[9px] font-black text-emerald-600">{{ $items->where('status', 'approved')->count() }}✓</span>
                                        <span class="text-[9px] font-black text-rose-500">{{ $items->where('status', 'rejected')->count() }}✗</span>
                                    </div>
                                </td>
                                <td class="pe-8 py-5 text-end">
                                    <a href="{{ route('auditor.approved_items.show', $submission->submission_id) }}" 
                                    class="inline-block bg-slate-900 text-white px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-indigo-600 transition-all">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-8">
            {{ $submissions->links() }}
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const initFilter = (parentID, wrapperID, childID) => {
        const parent = document.getElementById(parentID);
        const wrapper = document.getElementById(wrapperID);
        const child = document.getElementById(childID);
        if(!parent || !child) return;
        const options = Array.from(child.options);

        const runFilter = () => {
            const val = parent.value;
            if(wrapper) wrapper.classList.toggle('hidden', !val);
            child.innerHTML = '';
            child.appendChild(options[0]);
            options.forEach(opt => {
                if(opt.getAttribute('data-parent') === val) child.appendChild(opt);
            });
        };
        parent.addEventListener('change', runFilter);
        if(parent.value) runFilter();
    };

    initFilter('facultySelect', 'departmentWrapper', 'departmentSelect');
    initFilter('officeSelect', 'unitWrapper', 'unitSelect'); // Fixed Unit logic
    initFilter('categorySelect', 'subcategoryWrapper', 'subcategorySelect');
});
</script>
@endsection