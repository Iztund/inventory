@extends('layouts.admin')

@section('title', 'Verified Asset Registry - Admin Portal')

@section('content')

<div class="min-h-screen bg-slate-50 py-8">
<div class="container mx-auto px-4 max-w-7xl">

    {{-- 1. HEADER --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
        <div class="flex items-center gap-4">
            <div class="min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
                    </span>
                    <h6 class="text-[10px] font-black uppercase tracking-widest text-slate-500 truncate">
                        College of Medicine Central Registry
                    </h6>
                </div>
                <h1 class="text-xl md:text-3xl font-black text-slate-900 tracking-tight truncate">
                    Verified Asset Registry
                </h1>
            </div>
        </div>

        {{-- SUMMARY BADGE --}}
        @php
            $totalItems = $submissions->sum(fn($s) => $s->items->count());
        @endphp
        <div class="bg-white border-2 border-emerald-100 rounded-2xl p-2 shadow-md flex items-center self-start lg:self-center shrink-0">
            <div class="relative flex items-center justify-center p-2">
                <div class="w-10 h-10 bg-emerald-600 rounded-xl flex items-center justify-center text-white relative z-10 shadow-lg">
                    <span class="text-sm font-black">{{ $submissions->total() }}</span>
                </div>
            </div>
            <div class="ml-3 pr-4">
                <div class="text-[9px] font-black text-slate-600 uppercase tracking-widest">{{ $totalItems }} Item(s) Across Batches</div>
                <div class="text-xs font-black text-emerald-600 uppercase">
                    {{ $submissions->total() }} Verified Batch(es)
                </div>
            </div>
        </div>
    </div>

    {{-- 2. SEARCH & FILTER BAR --}}
    <div class="mb-8">
        <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border-t-4 border-amber-500 overflow-hidden">
            <div class="p-6">
                <form action="{{ route('admin.approved_items.index') }}" method="GET" id="filterForm">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                        
                        {{-- Full-Width Search Bar with Auditor Focus Ring --}}
                        <div class="col-span-12">
                            <div class="flex items-center bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-amber-500/20 transition-all">
                                <span class="pl-4 pr-2 text-slate-400"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" 
                                    class="w-full border-0 bg-transparent py-4 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:ring-0" 
                                    placeholder="Search Asset Tag, Serial, or Item Name..." 
                                    value="{{ request('search') }}">
                                
                                @if(request('search'))
                                    <a href="{{ route('admin.approved_items.index') }}" class="px-3 text-slate-300 hover:text-rose-500 transition-colors">
                                        <i class="fas fa-times-circle text-lg"></i>
                                    </a>
                                @endif

                                <button type="submit" class="bg-admin-navy text-white px-10 py-4 font-black text-xs uppercase tracking-widest hover:bg-amber-500 transition-colors">
                                    SEARCH REGISTRY
                                </button>
                            </div>
                        </div>

                        {{-- Academic Branch (Indigo Style) --}}
                        <div class="col-span-12 lg:col-span-3">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-white h-full shadow-sm">
                                <h6 class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-3 italic">Academic Hierarchy</h6>
                                <div class="flex flex-col gap-2">
                                    <select name="faculty_id" id="facultySelect" class="w-full text-[11px] font-bold bg-white border-slate-200 rounded-xl py-2.5 focus:ring-amber-500/10">
                                        <option value="">-- Faculty --</option>
                                        @foreach($faculties as $f)
                                            <option value="{{ $f->faculty_id }}" {{ request('faculty_id') == $f->faculty_id ? 'selected' : '' }}>{{ $f->faculty_name }}</option>
                                        @endforeach
                                    </select>
                                    <div id="departmentWrapper" class="{{ request('faculty_id') ? '' : 'hidden' }}">
                                        <select name="dept_id" id="departmentSelect" class="w-full text-[11px] font-bold bg-amber-50 border-amber-100 text-amber-900 rounded-xl py-2.5">
                                            <option value="">-- Dept --</option>
                                            @foreach($departments as $d)
                                                <option value="{{ $d->dept_id }}" data-parent="{{ $d->faculty_id }}" {{ request('dept_id') == $d->dept_id ? 'selected' : '' }}>{{ $d->dept_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Admin Branch (Sky Style) --}}
                        <div class="col-span-12 lg:col-span-3">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-white h-full shadow-sm">
                                <h6 class="text-[10px] font-black text-sky-600 uppercase tracking-widest mb-3 italic">Administrative Node</h6>
                                <div class="flex flex-col gap-2">
                                    <select name="office_id" id="officeSelect" class="w-full text-[11px] font-bold bg-white border-slate-200 rounded-xl py-2.5 focus:ring-amber-500/10">
                                        <option value="">-- Office --</option>
                                        @foreach($offices as $o)
                                            <option value="{{ $o->office_id }}" {{ request('office_id') == $o->office_id ? 'selected' : '' }}>{{ $o->office_name }}</option>
                                        @endforeach
                                    </select>
                                    <div id="unitWrapper" class="{{ request('office_id') ? '' : 'hidden' }}">
                                        <select name="unit_id" id="unitSelect" class="w-full text-[11px] font-bold bg-amber-50 border-amber-100 text-amber-900 rounded-xl py-2.5">
                                            <option value="">-- Unit --</option>
                                            @foreach($units as $u)
                                                <option value="{{ $u->unit_id }}" data-parent="{{ $u->office_id }}" {{ request('unit_id') == $u->unit_id ? 'selected' : '' }}>{{ $u->unit_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Research Units (Emerald Style) --}}
                        <div class="col-span-12 lg:col-span-3">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-white h-full shadow-sm">
                                <h6 class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-3 italic">Specialized Units</h6>
                                <select name="institute_id" class="w-full text-[11px] font-bold bg-white border-slate-200 rounded-xl py-2.5 focus:ring-amber-500/10">
                                    <option value="">-- Institute --</option>
                                    @foreach($institutes as $i)
                                        <option value="{{ $i->institute_id }}" {{ request('institute_id') == $i->institute_id ? 'selected' : '' }}>{{ $i->institute_name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[9px] text-slate-600 font-bold mt-3 uppercase tracking-tighter italic px-1">Independent Research Bodies</p>
                            </div>
                        </div>

                        {{-- Asset Classification (Navy Style) --}}
                        <div class="col-span-12 lg:col-span-3">
                            <div class="p-4 rounded-2xl bg-admin-navy border border-slate-800 h-full shadow-lg">
                                <h6 class="text-[10px] font-black text-amber-400 uppercase tracking-widest mb-3 italic">Asset Classification</h6>
                                <div class="flex flex-col gap-2">
                                    <select name="category_id" id="categorySelect" class="w-full text-[11px] font-bold bg-white border-0 rounded-xl py-2.5 focus:ring-2 focus:ring-amber-500">
                                        <option value="">-- Category --</option>
                                        @foreach($categories as $c)
                                            <option value="{{ $c->category_id }}" {{ request('category_id') == $c->category_id ? 'selected' : '' }}>{{ $c->category_name }}</option>
                                        @endforeach
                                    </select>
                                    <div id="subcategoryWrapper" class="transition-all duration-300 {{ request('sub_id') ? '' : 'hidden' }}">
                                        <p class="text-[9px] font-black text-amber-500/80 uppercase tracking-widest mb-1.5 ml-2">Refine Selection</p>
                                        
                                        <div class="relative group">
                                            <select name="sub_id" id="subcategorySelect" 
                                                class="w-full text-[11px] font-bold bg-slate-900 border-0 rounded-[1.25rem] pl-4 pr-10 py-3 text-white focus:ring-2 focus:ring-amber-500 shadow-xl appearance-none cursor-pointer">
                                                
                                                <option value="">-- Choose Sub-Type --</option>
                                                
                                                @foreach($subcategories as $s)
                                                    {{-- IMPORTANT: data-parent must match the category_id --}}
                                                    <option value="{{ $s->subcategory_id }}" 
                                                            data-parent="{{ $s->category_id }}" 
                                                            class="bkg-slate-900 text-white" 
                                                            {{ request('sub_id') == $s->subcategory_id ? 'selected' : '' }}>
                                                        {{ $s->subcategory_name }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            {{-- Custom Arrow positioned to not overlap text --}}
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-amber-400 group-hover:text-white transition-colors">
                                                <i class="fas fa-chevron-down text-[10px]"></i>
                                            </div>
                                        </div>
                                    </div>
                                
                                </div>
                            </div>
                        </div>

                        {{-- Bottom Action Row --}}
                        <div class="col-span-12 flex items-center justify-end gap-6 mt-2 border-t border-slate-100 pt-5">
                            <a href="{{ route('admin.approved_items.index') }}" class="text-[10px] font-black text-slate-600 hover:text-rose-500 uppercase tracking-widest transition-colors no-underline">
                                <i class="fas fa-undo-alt me-1"></i> CLEAR ALL FILTERS
                            </a>
                            <button type="submit" class="bg-amber-500 text-admin-navy px-12 py-3 rounded-full font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 transition-all shadow-md shadow-amber-200">
                                FILTER REGISTRY
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- 3. COMBINED REGISTRY STATS --}}
<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
    @php
        $statsCards = [
            // Registry Totals (Accurate across all pages)
            ['label' => 'Verified Assets', 'value' => number_format($totalItemsCount), 'icon' => 'fa-check-double', 'bg' => '#f0f9ff', 'fg' => '#0369a1'],
            ['label' => 'Total Valuation', 'value' => '₦' . number_format($totalValue, 0), 'icon' => 'fa-landmark', 'bg' => '#f5f3ff', 'fg' => '#6d28d9'],
            
            // Acquisition Types (Accurate across all pages)
            ['label' => 'New Acquisitions', 'value' => $countNew, 'icon' => 'fa-plus-circle', 'bg' => '#ecfdf5', 'fg' => '#059669'],
            ['label' => 'Internal Transfers', 'value' => $countTransfer, 'icon' => 'fa-route', 'bg' => '#eff6ff', 'fg' => '#2563eb'],
            
            // Maintenance & Disposal (Accurate across all pages)
            ['label' => 'Repair Logs', 'value' => $countRepair, 'icon' => 'fa-tools', 'bg' => '#fffbeb', 'fg' => '#d97706'],
            ['label' => 'Disposed Items', 'value' => $countDisposal, 'icon' => 'fa-trash-alt', 'bg' => '#fff1f2', 'fg' => '#e11d48'],
        ];
    @endphp

    @foreach($statsCards as $card)
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4 transition-transform hover:scale-[1.02]">
            {{-- Icon Container --}}
            <div class="shrink-0 w-11 h-11 rounded-xl flex items-center justify-center text-sm shadow-inner" 
                style="background-color: {{ $card['bg'] }}; color: {{ $card['fg'] }};">
                <i class="fas {{ $card['icon'] }}"></i>
            </div>
            
            {{-- Label & Value --}}
            <div class="min-w-0">
                <div class="text-[9px] font-black text-slate-500 uppercase tracking-widest mb-0.5">{{ $card['label'] }}</div>
                <div class="text-base font-black text-slate-900 leading-none truncate">
                    {{ $card['value'] }}
                </div>
            </div>
        </div>
    @endforeach
</div>

    {{-- 4. MAIN TABLE --}}
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">

        {{-- DESKTOP TABLE --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-900 text-white">
                        <th class="ps-8 py-4 text-[10px] font-black uppercase tracking-widest">Batch / Items</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Submitted By</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Reviewed By</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Status</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Value</th>
                        <th class="pe-8 py-4 text-[10px] font-black uppercase tracking-widest text-end">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($submissions as $submission)
                        @php
                            // Only show approved items in the name summary; fall back to all if none approved
                            $approvedItems  = $submission->items;
                            $displayItems   = $approvedItems->isNotEmpty() ? $approvedItems : $submission->items;
                            $itemNames      = $displayItems->pluck('item_name')->unique()->map(fn($n) => strtoupper($n))->implode(' / ');
                            $batchValue     = $submission->items->sum(fn($i) => ($i->cost ?? 0) * ($i->quantity ?? 1));
                            $isApproved     = $submission->status === 'approved';
                        @endphp

                        <tr class="hover:bg-emerald-50/30 transition-colors group">
                            {{-- Batch Ref + Item Names --}}
                            <td class="ps-8 py-5 align-middle">
                                <div class="flex flex-col">
                                    <span class="text-[13px] font-black text-slate-800 uppercase tracking-tight leading-none">
                                        {{ $itemNames ?: 'No Items' }}
                                    </span>
                                    @if($displayItems->count() > 0)
                                        <span class="text-[9px] font-bold text-slate-600 uppercase mt-1">
                                            Total Qty: {{ $displayItems->sum('quantity') }} Units
                                        </span>
                                    @endif
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[9px] text-slate-700 font-bold uppercase tracking-widest">
                                            Ref #{{ str_pad($submission->submission_id, 5, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <span class="text-slate-500">•</span>
                                        <span class="text-[9px] text-slate-600 font-bold uppercase">
                                            {{ $submission->items->count() }} Item(s)
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Submitted By --}}
                            <td class="px-4 py-5 align-middle">
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-black text-slate-800 uppercase">
                                        {{ $submission->submittedBy->profile->full_name ?? $submission->submittedBy->username ?? 'Unknown' }}
                                    </span>
                                    
                                    <span class="text-[9px] text-slate-600 font-bold italic uppercase tracking-wider">
                                        @php
                                            $user = $submission->submittedBy;
                                            $location = $user->department->dept_name 
                                                ?? $user->unit->unit_name 
                                                ?? $user->office->office_name 
                                                ?? $user->institute->institute_name 
                                                ?? $user->faculty->faculty_name 
                                                ?? 'General Registry';
                                        @endphp
                                        {{ $location }}
                                    </span>
                                </div>
                            </td>

                            {{-- Reviewed By --}}
                            <td class="px-4 py-5 align-middle">
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-black text-slate-800 uppercase">
                                        {{ $submission->reviewedBy->profile->full_name ?? $submission->reviewedBy->username ?? '—' }}
                                    </span>
                                    <span class="text-[9px] text-slate-600 font-bold italic uppercase">
                                        {{ $submission->updated_at ? $submission->updated_at->format('d M Y') : '—' }}
                                    </span>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-5 align-middle">
                                @php
                                    $counts = $submission->items->groupBy('status')->map->count();
                                    $approvedCount = $counts->get('approved', 0);
                                    $rejectedCount = $counts->get('rejected', 0);
                                    $totalItems    = $submission->items->count();
                                @endphp

                                <div class="flex flex-col gap-1.5">
                                    {{-- Main Batch Status --}}
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wide self-start"
                                        style="background-color:{{ $isApproved ? '#ecfdf5' : ($submission->status === 'pending' ? '#fef3c7' : '#fff1f2') }}; 
                                                color:{{ $isApproved ? '#059669' : ($submission->status === 'pending' ? '#d97706' : '#e11d48') }};">
                                        <i class="fas {{ $isApproved ? 'fa-check-double' : ($submission->status === 'pending' ? 'fa-clock' : 'fa-exclamation-triangle') }}"></i>
                                        {{ $submission->status }}
                                    </span>

                                    {{-- Mini Indicator Bar for Mixed Batches --}}
                                    @if($totalItems > 0)
                                        <div class="flex items-center gap-1 mt-1">
                                            @if($approvedCount > 0)
                                                <span class="flex items-center gap-0.5 bg-emerald-100 text-emerald-700 px-1.5 py-0.5 rounded text-[8px] font-black uppercase">
                                                    {{ $approvedCount }} approved
                                                </span>
                                            @endif
                                            
                                            @if($rejectedCount > 0)
                                                <span class="flex items-center gap-0.5 bg-rose-100 text-rose-700 px-1.5 py-0.5 rounded text-[8px] font-black uppercase">
                                                    {{ $rejectedCount }} REJECTED
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Batch Value --}}
                            <td class="px-4 py-5 align-middle">
                                <span class="text-[12px] font-black text-slate-800">
                                    ₦{{ number_format($batchValue, 2) }}
                                </span>
                            </td>

                            {{-- Action --}}
                            <td class="pe-8 py-5 align-middle text-end">
                                <div class="flex flex-col items-end gap-2">
                                    <a href="{{ route('admin.submissions.show', $submission->submission_id) }}"
                                    class="inline-flex items-center gap-2 bg-slate-900 text-white px-6 py-2.5 rounded-[1.25rem] text-[10px] font-black uppercase tracking-widest hover:bg-amber-500 hover:shadow-lg hover:shadow-amber-500/20 transition-all duration-300 group">
                                        
                                        <span>Manage Batch</span>
                                        
                                        {{-- Animated Arrow on Hover --}}
                                        <i class="fas fa-arrow-right text-[9px] transform group-hover:translate-x-1 transition-transform"></i>
                                    </a>

                                    {{-- Optional: Quick Date Reference --}}
                                    <span class="text-[9px] font-bold text-slate-400 uppercase mr-2">
                                        Updated {{ $submission->updated_at->format('M d, Y') }}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-20 text-center font-black text-slate-300 uppercase text-xs">
                                No verified batches found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MOBILE CARDS --}}
        <div class="md:hidden divide-y divide-slate-100">
            @foreach($submissions as $submission)
                @php
                    $approvedItems = $submission->items->where('status', 'approved');
                    $displayItems  = $approvedItems->isNotEmpty() ? $approvedItems : $submission->items;
                    $itemNames     = $displayItems->pluck('item_name')->unique()->map(fn($n) => strtoupper($n))->implode(' / ');
                    $batchValue    = $submission->items->sum(fn($i) => ($i->cost ?? 0) * ($i->quantity ?? 1));
                    $isApproved    = $submission->status === 'approved';
                @endphp
                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-black text-emerald-600 uppercase">
                            Ref #{{ str_pad($submission->submission_id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase"
                              style="background-color:{{ $isApproved ? '#ecfdf5' : '#fff1f2' }}; color:{{ $isApproved ? '#059669' : '#e11d48' }};">
                            <i class="fas {{ $isApproved ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                            {{ $submission->status }}
                        </span>
                    </div>

                    <h3 class="text-sm font-black text-slate-900 uppercase mb-1">{{ $itemNames ?: 'No Items' }}</h3>

                    <p class="text-[10px] text-slate-500 font-bold uppercase mb-1">
                        {{ $submission->items->count() }} Item(s) • ₦{{ number_format($batchValue, 2) }}
                    </p>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mb-4">
                        By: {{ $submission->submittedBy->profile->full_name ?? $submission->submittedBy->username }}
                    </p>

                    <a href="{{ route('admin.submissions.show', $submission->submission_id) }}"
                       class="block text-center bg-slate-900 text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest">
                        View File
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    {{-- 5. PAGINATION --}}
    <div class="mt-8">
        {{ $submissions->appends(request()->query())->links() }}
    </div>

</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log("Verified Registry Filter Initialized...");

    /**
     * @param {string} parentId - The ID of the primary select (e.g., facultySelect)
     * @param {string} childId  - The ID of the dependent select (e.g., departmentSelect)
     * @param {string} wrapperId - The ID of the container to show/hide
     */
    function setupFilter(parentId, childId, wrapperId) {
        const parent = document.getElementById(parentId);
        const child = document.getElementById(childId);
        const wrapper = document.getElementById(wrapperId);

        if (!parent || !child) return;

        // 1. Capture all options from the child select on page load
        const allOptions = Array.from(child.options);

        // 2. The Filter Logic
        const runFilter = (selectedId, currentChildValue = null) => {
            // Reset child
            child.innerHTML = '';
            child.appendChild(allOptions[0]); // Keep "-- Select --"

            if (selectedId) {
                const matches = allOptions.filter(opt => opt.getAttribute('data-parent') === String(selectedId));
                matches.forEach(opt => {
                    // Re-apply "selected" state if this matches what was in the URL
                    if (currentChildValue && opt.value === String(currentChildValue)) {
                        opt.selected = true;
                    }
                    child.appendChild(opt);
                });
                if (wrapper) wrapper.classList.remove('hidden');
            } else {
                if (wrapper) wrapper.classList.add('hidden');
            }
        };

        // 3. Event Listener for User Changes
        parent.addEventListener('change', function() {
            runFilter(this.value);
        });

        // 4. PERSISTENCE: Run on load if parent already has a value (from Request/URL)
        if (parent.value) {
            // We pass the existing child value to ensure it stays selected after reload
            const urlParams = new URLSearchParams(window.location.search);
            const childParamName = child.getAttribute('name'); 
            const existingChildValue = urlParams.get(childParamName);
            
            runFilter(parent.value, existingChildValue);
        }
    }

    // Initialize all hierarchies
    setupFilter('facultySelect', 'departmentSelect', 'departmentWrapper');
    setupFilter('officeSelect', 'unitSelect', 'unitWrapper');
    setupFilter('categorySelect', 'subcategorySelect', 'subcategoryWrapper');
});
</script>

@endsection