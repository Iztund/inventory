@extends('layouts.admin')

@section('title', 'Pending Item Reviews - Admin Portal')

@section('content')

<div class="min-h-screen bg-slate-50 py-8">
    <div class="container mx-auto px-4 max-w-7xl">

        {{-- 1. HEADER & ACTION BAR --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-8">
            <div class="flex items-center gap-4">
        <a href="{{ url()->previous() }}" 
        class="flex items-center justify-center w-10 h-10 rounded-xl border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-indigo-600 hover:border-indigo-100 transition-all duration-200 shadow-sm group">
            <i class="fas fa-arrow-left text-sm group-hover:-translate-x-0.5 transition-transform"></i>
        </a>

        <div class="min-w-0">
            <div class="flex items-center gap-2 mb-0.5">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-600"></span>
                </span>
                <h6 class="text-[10px] font-black uppercase tracking-widest text-slate-500 truncate">
                    College of Medicine Central Registry
                </h6>
            </div>
            <h1 class="text-xl md:text-3xl font-black text-slate-900 tracking-tight truncate">
                Pending Asset Reviews
            </h1>
        </div>
    </div>
        
        {{-- LIVE QUEUE STATUS --}}
        @php
            // Total individual items across all pending submissions (not just this page)
            $totalItemCount = $submissions->sum(fn($s) => $s->items->count());
        @endphp
        <div class="bg-white border-2 border-amber-100 rounded-2xl p-2 shadow-md flex items-center self-start lg:self-center shrink-0">
            <div class="relative flex items-center justify-center p-2">
                <div class="w-10 h-10 bg-amber-600 rounded-xl flex items-center justify-center text-white relative z-10 shadow-lg">
                    <span class="text-sm font-black">{{ $submissions->total() }}</span>
                </div>
            </div>
            <div class="ml-3 pr-4">
                <div class="text-[9px] font-black text-slate-400 uppercase tracking-widest">{{ $totalItemCount }} Individual Item(s)</div>
                <div class="text-xs font-black text-amber-600 uppercase">
                    {{ $submissions->total() }} Batch(es) Awaiting Review
                </div>
            </div>
        </div>
    </div>

    {{-- 2. SEARCH & FILTER BAR (Mirror of Approved Items Design) --}}
    <div class="mb-8">
        <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border-t-4 border-amber-500 overflow-hidden">
            <div class="p-6">
                <form action="{{ route('admin.submissions.pending') }}" method="GET" id="filterForm">
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                        
                        {{-- Full-Width Search Bar --}}
                        <div class="col-span-12">
                            <div class="flex items-center bg-slate-50 border border-slate-200 rounded-2xl overflow-hidden focus-within:ring-2 focus-within:ring-amber-500/20 transition-all">
                                <span class="pl-4 pr-2 text-slate-400"><i class="fas fa-search"></i></span>
                                <input type="text" name="search" 
                                    class="w-full border-0 bg-transparent py-4 text-sm font-semibold text-slate-700 placeholder:text-slate-400 focus:ring-0" 
                                    placeholder="Search Item Name, Serial #, Batch ID, or Staff..." 
                                    value="{{ request('search') }}">
                                
                                @if(request('search'))
                                    <a href="{{ route('admin.submissions.pending') }}" class="px-3 text-slate-300 hover:text-rose-500 transition-colors">
                                        <i class="fas fa-times-circle text-lg"></i>
                                    </a>
                                @endif

                                <button type="submit" class="bg-slate-900 text-white px-10 py-4 font-black text-xs uppercase tracking-widest hover:bg-amber-500 transition-colors">
                                    SEARCH PENDING
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
                                <h6 class="text-[10px] font-black text-sky-600 uppercase tracking-widest mb-3 italic">Administrative</h6>
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
                            </div>
                        </div>

                        {{-- Asset Classification (Navy Style) --}}
                        <div class="col-span-12 lg:col-span-3">
                            <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 h-full shadow-lg">
                                <h6 class="text-[10px] font-black text-amber-400 uppercase tracking-widest mb-3 italic">Asset Classification</h6>
                                <div class="flex flex-col gap-2">
                                    <select name="category_id" id="categorySelect" class="w-full text-[11px] font-bold bg-white border-0 rounded-xl py-2.5 focus:ring-2 focus:ring-amber-500">
                                        <option value="">-- Category --</option>
                                        @foreach($categories as $c)
                                            <option value="{{ $c->category_id }}" {{ request('category_id') == $c->category_id ? 'selected' : '' }}>{{ $c->category_name }}</option>
                                        @endforeach
                                    </select>
                                    <div id="subcategoryWrapper" class="{{ request('category_id') ? '' : 'hidden' }}">
                                        <div class="relative group">
                                            <select name="sub_id" id="subcategorySelect" 
                                                class="w-full text-[11px] font-bold bg-slate-800 border-0 rounded-[1.25rem] pl-4 pr-10 py-3 text-white focus:ring-2 focus:ring-amber-500 appearance-none cursor-pointer">
                                                <option value="">-- Choose Sub-Type --</option>
                                                @foreach($subcategories as $s)
                                                    <option value="{{ $s->subcategory_id }}" data-parent="{{ $s->category_id }}" {{ request('sub_id') == $s->subcategory_id ? 'selected' : '' }}>
                                                        {{ $s->subcategory_name }}
                                                    </option>
                                                @endforeach
                                            </select>
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
                            <a href="{{ route('admin.submissions.pending') }}" class="text-[10px] font-black text-slate-600 hover:text-rose-500 uppercase tracking-widest transition-colors no-underline">
                                <i class="fas fa-undo-alt me-1"></i> CLEAR ALL PENDING FILTERS
                            </a>
                            <button type="submit" class="bg-amber-500 text-slate-900 px-12 py-3 rounded-full font-black text-[10px] uppercase tracking-widest hover:bg-amber-600 transition-all shadow-md shadow-amber-200">
                                FILTER QUEUE
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- 3. DYNAMIC STATS GRID --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        @php
            $stats = [
                ['label' => 'New Acquisitions', 'count' => $submissions->where('submission_type', 'new_purchase')->count(), 'icon' => 'fa-plus-circle', 'color' => 'emerald'],
                ['label' => 'Internal Transfers', 'count' => $submissions->where('submission_type', 'transfer')->count(), 'icon' => 'fa-route', 'color' => 'blue'],
                ['label' => 'Repair Logs',        'count' => $submissions->where('submission_type', 'maintenance')->count(), 'icon' => 'fa-tools', 'color' => 'amber'],
                ['label' => 'Disposal Requests',  'count' => $submissions->where('submission_type', 'disposal')->count(), 'icon' => 'fa-trash-alt', 'color' => 'rose'],
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm"
                 style="background-color: {{ match($stat['color']) { 'emerald' => '#ecfdf5', 'blue' => '#eff6ff', 'amber' => '#fffbeb', 'rose' => '#fff1f2' } }}; color: {{ match($stat['color']) { 'emerald' => '#059669', 'blue' => '#2563eb', 'amber' => '#d97706', 'rose' => '#e11d48' } }};">
                <i class="fas {{ $stat['icon'] }}"></i>
            </div>
            <div>
                <div class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">{{ $stat['label'] }}</div>
                <div class="text-lg font-black text-slate-900 leading-none">{{ $stat['count'] }}</div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- 4. MAIN TABLE --}}
    <div class="bg-white rounded-[2rem] border border-slate-200 shadow-sm overflow-hidden">
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-900 text-white">
                        <th class="ps-8 py-4 text-[10px] font-black uppercase tracking-widest">Batch / Items</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Serial Number(s)</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Submitted By</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Classification</th>
                        <th class="px-4 py-4 text-[10px] font-black uppercase tracking-widest">Date</th>
                        <th class="pe-8 py-4 text-[10px] font-black uppercase tracking-widest text-end">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($submissions as $submission)
                        @php
                            $type_style = [
                                'new_purchase' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'label' => 'New Entry'],
                                'transfer'     => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',    'label' => 'Transfer'],
                                'maintenance'  => ['bg' => 'bg-amber-50',   'text' => 'text-amber-700',   'label' => 'Repair'],
                                'disposal'     => ['bg' => 'bg-rose-50',    'text' => 'text-rose-700',    'label' => 'Disposal'],
                            ][$submission->submission_type ?? ''] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-700', 'label' => 'General'];

                            $itemNames    = $submission->items->pluck('item_name')->map(fn($n) => strtoupper($n))->implode(', ');
                            $serialNumbers = $submission->items->pluck('serial_number')->filter()->implode(', ');
                        @endphp
                        <tr class="hover:bg-amber-50/30 transition-colors group">
                            {{-- Batch + Item Names --}}
                            <td class="ps-8 py-5 align-middle">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-800 uppercase tracking-tight">
                                        {{ $itemNames ?: 'No Items' }}
                                    </span>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                                        Batch #{{ str_pad($submission->submission_id, 5, '0', STR_PAD_LEFT) }} • {{ $submission->items->count() }} Item(s)
                                    </span>
                                </div>
                            </td>
                            {{-- Serial Numbers --}}
                            <td class="px-4 py-5 align-middle">
                                <code class="text-[11px] font-black bg-slate-100 px-2 py-1 rounded text-slate-600">
                                    {{ $serialNumbers ?: 'NO-SERIAL' }}
                                </code>
                            </td>
                            {{-- Submitted By --}}
                            <td class="px-4 py-5 align-middle">
                                <div class="flex flex-col">
                                    <span class="text-[11px] font-black text-slate-800 uppercase">
                                        {{ $submission->submittedBy->profile->full_name ?? $submission->submittedBy->username ?? 'Unknown' }}
                                    </span>
                                    <span class="text-[9px] text-slate-400 font-bold italic uppercase">
                                        {{ $submission->submittedBy->department->dept_name ?? $submission->submittedBy->faculty->faculty_name ?? 'Registry' }}
                                    </span>
                                </div>
                            </td>
                            {{-- Classification --}}
                            <td class="px-4 py-5 align-middle">
                                <span class="inline-flex items-center {{ $type_style['bg'] }} {{ $type_style['text'] }} px-2.5 py-0.5 rounded-md text-[10px] font-black uppercase tracking-wide">
                                    {{ $type_style['label'] }}
                                </span>
                            </td>
                            {{-- Submitted At --}}
                            <td class="px-4 py-5 align-middle">
                                <span class="text-[11px] font-black text-slate-600">
                                    {{ $submission->submitted_at ? $submission->submitted_at->format('d M Y') : ($submission->created_at->format('d M Y')) }}
                                </span>
                                <span class="text-[9px] text-slate-400 font-bold d-block">
                                    {{ $submission->submitted_at ? $submission->submitted_at->format('h:i A') : ($submission->created_at->format('h:i A')) }}
                                </span>
                            </td>
                            {{-- Action --}}
                            <td class="pe-8 py-5 align-middle text-end">
                                <a href="{{ route('admin.submissions.show', $submission->submission_id) }}" 
                                   class="bg-slate-900 text-white px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-amber-600 transition-all">
                                    Inspect
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-20 text-center font-black text-slate-300 uppercase text-xs">
                                No batches awaiting review.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MOBILE VIEW --}}
        <div class="md:hidden divide-y divide-slate-100">
            @foreach($submissions as $submission)
                @php
                    $itemNames = $submission->items->pluck('item_name')->map(fn($n) => strtoupper($n))->implode(', ');
                @endphp
                <div class="p-5">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-black text-amber-600 uppercase">
                            Batch #{{ str_pad($submission->submission_id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                        <span class="text-[9px] font-bold text-slate-400 italic">
                            {{ $submission->submitted_at ? $submission->submitted_at->diffForHumans() : $submission->created_at->diffForHumans() }}
                        </span>
                    </div>
                    <h3 class="text-sm font-black text-slate-900 uppercase mb-1">{{ $itemNames ?: 'No Items' }}</h3>
                    <p class="text-[10px] text-slate-500 font-bold uppercase mb-4">
                        {{ $submission->items->count() }} Item(s) • {{ $submission->submittedBy->profile->full_name ?? $submission->submittedBy->username }}
                    </p>
                    <a href="{{ route('admin.submissions.show', $submission->submission_id) }}" 
                       class="block text-center bg-slate-900 text-white py-3 rounded-xl font-black text-[10px] uppercase tracking-widest">
                        Inspect Batch
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="mt-8">
        {{ $submissions->appends(request()->query())->links() }}
    </div>
</div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log("Filter script initialized...");

    function setupFilter(parentId, childId, wrapperId) {
        const parent = document.getElementById(parentId);
        const child = document.getElementById(childId);
        const wrapper = document.getElementById(wrapperId);

        if (!parent || !child) {
            console.error(`Missing elements: ${parentId} or ${childId}`);
            return;
        }

        // Store all original options to filter from
        const allOptions = Array.from(child.options);

        parent.addEventListener('change', function() {
            const selectedId = this.value;
            console.log(`Parent ${parentId} changed to: ${selectedId}`);

            // 1. Reset child
            child.innerHTML = '';
            child.appendChild(allOptions[0]); // Keep the placeholder

            // 2. Filter and show
            if (selectedId) {
                const matches = allOptions.filter(opt => opt.getAttribute('data-parent') === selectedId);
                matches.forEach(opt => child.appendChild(opt));
                
                if(wrapper) wrapper.classList.remove('hidden');
            } else {
                if(wrapper) wrapper.classList.add('hidden');
            }
        });
    }

    // Initialize the connections
    setupFilter('facultySelect', 'departmentSelect', 'departmentWrapper');
    setupFilter('officeSelect', 'unitSelect', 'unitWrapper');
    setupFilter('categorySelect', 'subcategorySelect', 'subcategoryWrapper');
});
</script>
@endsection