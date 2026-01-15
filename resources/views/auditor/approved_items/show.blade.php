@extends('layouts.auditor')

@php
    $isSingleAsset = isset($asset);
    $displayItems = $isSingleAsset ? collect([$latestSnapshot ?? $asset->latestSnapshot]) : $submission->items;
    $headerRef = $isSingleAsset ? $asset->asset_tag : str_pad($submission->submission_id, 7, '0', STR_PAD_LEFT);
@endphp

@section('title', 'Batch Verification - REF: #' . $headerRef)

@section('content')
<div class="container-fluid py-5 bg-slate-50 min-vh-100">
    
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4 px-4">
        <div class="flex items-center">
            <a href="{{ url()->previous() }}" class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm border-2 border-slate-200 text-slate-500 hover:scale-110 transition-all">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="ms-4">
                <h3 class="font-black text-slate-800 m-0 text-2xl uppercase tracking-tighter">Batch Verification Record</h3>
                <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                    Reference: #{{ $headerRef }} &bullet; {{ $displayItems->count() }} Total Items
                </div>
            </div>
        </div>
    </div>

    {{-- GENERAL NOTES --}}
    <div class="row g-4 mb-8 px-4">
        <div class="col-md-12">
            <div class="h-full p-6 bg-white border-l-8 border-slate-900 rounded-r-3xl shadow-sm">
                <h6 class="font-black text-slate-400 uppercase text-[10px] tracking-widest mb-2">General Submission Note</h6>
                <p class="text-slate-800 font-bold italic m-0 text-lg">
                    "{{ $submission->note ?? $submission->remarks ?? 'No general notes provided for this batch.' }}"
                </p>
            </div>
        </div>
    </div>

    @foreach($displayItems as $item)
        @php
            $uID = $item->submission_item_id;
            $itemAsset = $item->asset; 
            $displayName = $item->item_name ?? ($itemAsset->item_name ?? 'Unnamed Item');
            $currentStatus = strtoupper($item->status ?? 'UNKNOWN'); 
            $isRejected = ($currentStatus === 'REJECTED');

            // Location Logic (Faculties, Departments, Offices, Units, Institutes)
            $fullLocation = 'College of Medicine';
            if ($itemAsset) {
                if ($itemAsset->unit && $itemAsset->office) {
                    $fullLocation = $itemAsset->office->office_name . ' • ' . $itemAsset->unit->unit_name;
                } elseif ($itemAsset->department && $itemAsset->faculty) {
                    $fullLocation = $itemAsset->faculty->faculty_name . ' • ' . $itemAsset->dept_name;
                } elseif ($itemAsset->institute) {
                    $fullLocation = $itemAsset->institute->institute_name;
                } elseif ($itemAsset->office) {
                    $fullLocation = $itemAsset->office->office_name;
                } elseif ($itemAsset->faculty) {
                    $fullLocation = $itemAsset->faculty->faculty_name;
                }
            }

            // FIX: Array to String conversion safety
            $docPath = $item->document_path;
            if (is_array($docPath)) {
                $docPath = $docPath[0] ?? null;
            }

            $theme = [
                'bg' => $isRejected ? 'bg-rose-600' : 'bg-emerald-600',
                'text' => $isRejected ? 'text-rose-600' : 'text-emerald-600',
                'border' => $isRejected ? 'border-rose-600' : 'border-emerald-600',
                'soft-bg' => $isRejected ? 'bg-rose-50' : 'bg-emerald-50',
                'pulse' => $isRejected ? 'animate-pulse bg-rose-400' : 'animate-pulse bg-emerald-400'
            ];
        @endphp

        <div class="row g-4 mb-5 border-b border-slate-200 pb-10 px-3">
            {{-- Left Identity Column --}}
            <div class="col-lg-4">
                <div class="bg-white rounded-3xl shadow-sm border-t-8 {{ $theme['border'] }} p-8 text-center sticky-top" style="top: 20px; z-index: 10;">
                    <div class="{{ $theme['soft-bg'] }} w-24 h-24 rounded-full inline-flex items-center justify-center mb-4 border-4 border-white shadow-inner relative mx-auto">
                        <div class="absolute inset-0 rounded-full {{ $theme['pulse'] }} opacity-20"></div>
                        <i class="fas {{ $isRejected ? 'fa-circle-xmark' : 'fa-circle-check' }} {{ $theme['text'] }} text-4xl relative z-10"></i>
                    </div>
                    
                    <h4 class="font-black text-slate-800 mb-1 uppercase tracking-tight text-xl">{{ $displayName }}</h4>
                    <div class="{{ $theme['text'] }} font-black text-xs font-mono mb-4 tracking-widest uppercase">{{ $itemAsset->asset_tag ?? 'NEW ENTRY' }}</div>
                    
                    <div class="flex items-center justify-center gap-2 mb-6">
                        <span class="{{ $theme['bg'] }} text-white px-3 py-1 rounded-full font-black text-[9px] uppercase tracking-widest shadow-sm">
                            {{ $currentStatus }}
                        </span>
                        {{-- Added Quantity Badge --}}
                        <span class="bg-slate-800 text-white px-3 py-1 rounded-full font-black text-[9px] uppercase tracking-widest shadow-sm">
                            QTY: {{ $item->quantity ?? 1 }}
                        </span>
                    </div>

                    <div class="text-left space-y-4 pt-6 border-t border-slate-100">
                        <div>
                            <label class="text-slate-400 font-black uppercase text-[9px] tracking-widest block mb-1">Deployment Location</label>
                            <p class="text-slate-800 font-bold text-sm m-0 uppercase italic leading-tight">
                                <i class="fas fa-map-marker-alt {{ $theme['text'] }} me-1"></i> {{ $fullLocation }}
                            </p>
                        </div>

                        <div class="p-3 bg-amber-50 rounded-xl border border-amber-100">
                            <label class="text-amber-600 font-black uppercase text-[8px] tracking-widest block mb-1">Staff Item Note</label>
                            <p class="text-[11px] text-slate-700 font-bold m-0 italic">
                                "{{ $item->item_note ?? $item->note ?? 'No item-specific notes.' }}"
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Details Column --}}
            <div class="col-lg-8">
                <div class="{{ $theme['soft-bg'] }} rounded-3xl p-6 mb-6 border-2 border-dashed {{ $theme['border'] }}">
                    <div class="flex items-start">
                        <div class="bg-white w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm text-2xl {{ $theme['text'] }} me-4 shrink-0">
                            <i class="fas fa-quote-left"></i>
                        </div>
                        <div>
                            <h6 class="font-black text-slate-800 uppercase text-[10px] tracking-widest mb-1 opacity-60">Auditor Remark</h6>
                            <p class="text-slate-800 font-bold text-lg italic leading-snug m-0">
                                "{{ $item->audit->comments ?? 'No auditor remarks for this record.' }}"
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-2 rounded-2xl shadow-sm mb-4 border border-slate-200">
                    <ul class="nav nav-pills nav-justified gap-2" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active rounded-xl py-3 font-black uppercase text-[11px] tracking-widest" data-bs-toggle="tab" data-bs-target="#specs-{{ $uID }}">
                                <i class="fas fa-file-lines me-2"></i> Technical & Value
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-xl py-3 font-black uppercase text-[11px] tracking-widest" data-bs-toggle="tab" data-bs-target="#trail-{{ $uID }}">
                                <i class="fas fa-history me-2"></i> Audit Trail
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="specs-{{ $uID }}">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 h-full">
                                        <small class="text-slate-400 font-black uppercase text-[9px] tracking-widest block mb-1">Main Category</small>
                                        <span class="text-slate-800 font-bold uppercase">{{ $item->category->category_name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 h-full">
                                        <small class="text-slate-400 font-black uppercase text-[9px] tracking-widest block mb-1">Sub-Category</small>
                                        <span class="text-slate-800 font-bold uppercase">{{ $item->subcategory->subcategory_name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                {{-- Added Quantity Field in Grid --}}
                                <div class="col-md-4">
                                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 h-full">
                                        <small class="text-slate-400 font-black uppercase text-[9px] tracking-widest block mb-1">Quantity</small>
                                        <span class="text-slate-800 font-black text-lg">{{ $item->quantity ?? 1 }} Units</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="p-4 bg-indigo-50 rounded-2xl border border-indigo-100 h-full">
                                        <small class="text-indigo-400 font-black uppercase text-[9px] tracking-widest block mb-1">Item Value (Cost)</small>
                                        <span class="text-indigo-900 font-black text-xl">
                                            ₦{{ number_format($item->cost ?? $item->unit_cost ?? 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 h-full">
                                        <small class="text-slate-400 font-black uppercase text-[9px] tracking-widest block mb-1">Serial Number</small>
                                        <span class="text-slate-800 font-bold font-mono">{{ $item->serial_number ?: 'N/A' }}</span>
                                    </div>
                                </div>

                                
                                <<div class="col-md-12">
    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
        <small class="text-slate-400 font-black uppercase text-[9px] tracking-widest block mb-2">Technical & Support Documents</small>
        
        @php
            // 1. Try to find the document path in different possible locations
            // Priority: Submission Item -> Latest Snapshot -> Original Asset
            $rawDocs = $item->document_path 
                       ?? ($item->snapshot->document_path ?? ($itemAsset->document_path ?? null));

            $docs = [];

            if ($rawDocs) {
                if (is_array($rawDocs)) {
                    // Already an array
                    $docs = $rawDocs;
                } elseif (is_string($rawDocs)) {
                    // Try to decode if it's a JSON string
                    $decoded = json_decode($rawDocs, true);
                    $docs = is_array($decoded) ? $decoded : [$rawDocs];
                }
            }
        @endphp

        @if(count($docs) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach($docs as $index => $path)
                    <div class="flex items-center justify-between p-2 bg-white rounded-xl border border-slate-200 shadow-sm hover:border-blue-300 transition-all">
                        <div class="flex items-center gap-3 overflow-hidden">
                            <div class="w-8 h-8 rounded-lg {{ $theme['soft-bg'] }} flex items-center justify-center {{ $theme['text'] }} shrink-0">
                                <i class="fas fa-file-invoice"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-[10px] font-black text-slate-700 truncate mb-0 uppercase tracking-tighter">
                                    {{ basename($path) == $path ? 'Document ' . ($index + 1) : basename($path) }}
                                </p>
                                <p class="text-[8px] text-slate-400 truncate uppercase">Uploaded File</p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $path) }}" target="_blank" class="ms-2 px-3 py-1.5 bg-slate-900 text-white rounded-lg font-black text-[9px] uppercase hover:bg-blue-600 transition-colors shrink-0">
                            View
                        </a>
                    </div>
                @endforeach
            </div>
        @else
            <div class="flex items-center gap-2 py-2 opacity-50">
                <i class="fas fa-file-circle-exclamation text-slate-400"></i>
                <span class="text-slate-400 italic text-[11px] font-bold tracking-tight uppercase">No documents attached</span>
            </div>
        @endif
    </div>
</div>
                            </div>
                        </div>
                        {{-- AUDIT TRAIL --}}
                        <div class="tab-pane fade" id="trail-{{ $uID }}">
                            <div class="space-y-6">
                                @php
                                    // 1. Get the record belonging to THIS specific submission attempt
                                    $currentDecision = $history->firstWhere('submission_item_id', $item->submission_item_id);

                                    // 2. Get previous lifecycle history (linked by asset_id but different submission)
                                    $pastLifecycle = $history->filter(function($record) use ($item) {
                                        return $record->asset_id === $item->asset_id && 
                                            $record->submission_item_id !== $item->submission_item_id;
                                    })->sortByDesc('created_at');
                                @endphp

                                {{-- SECTION: CURRENT DECISION --}}
                                @if($currentDecision)
                                    <div class="relative pb-4">
                                        <div class="text-[10px] font-black text-blue-600 uppercase mb-3 tracking-widest flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                                            Current Submission Decision
                                        </div>
                                        
                                        <div class="flex gap-4">
                                            <div class="shrink-0 text-center w-14 pt-2">
                                                <div class="font-black text-slate-800 text-xl">{{ $currentDecision->created_at->format('d') }}</div>
                                                <div class="text-[10px] font-black text-slate-400 uppercase">{{ $currentDecision->created_at->format('M') }}</div>
                                            </div>
                                            
                                            <div class="grow p-4 rounded-2xl border-2 border-blue-100 bg-white shadow-sm">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase tracking-widest {{ strtoupper($currentDecision->status) === 'REJECTED' ? 'bg-rose-600' : 'bg-emerald-600' }} text-white">
                                                        {{ $currentDecision->status }}
                                                    </span>
                                                    <span class="font-mono text-[9px] font-bold text-slate-400 uppercase">BATCH #{{ str_pad($currentDecision->submission_id, 7, '0', STR_PAD_LEFT) }}</span>
                                                </div>
                                                
                                                <p class="text-sm text-slate-800 font-bold italic mb-2">
                                                    "{{ $currentDecision->audit?->comments ?? ($currentDecision->remarks ?? 'No auditor remarks for this specific record.') }}"
                                                </p>
                                                
                                                <div class="text-[9px] font-black text-slate-400 uppercase tracking-tighter border-t pt-2 border-slate-50">
                                                    By: {{ $currentDecision->submission->reviewedBy->profile->first_name ?? 'System Auditor' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- SECTION: HISTORICAL LIFECYCLE --}}
                                @if($pastLifecycle->count() > 0)
                                    <div class="pt-4 border-t border-dashed border-slate-200">
                                        <div class="text-[10px] font-black text-slate-400 uppercase mb-4 tracking-widest">
                                            Asset Lifecycle History
                                        </div>

                                        <div class="space-y-4">
                                            @foreach($pastLifecycle as $record)
                                                <div class="flex gap-4 opacity-60 grayscale-[0.5] hover:grayscale-0 hover:opacity-100 transition-all">
                                                    <div class="shrink-0 text-center w-14">
                                                        <div class="font-bold text-slate-500 text-sm">{{ $record->created_at->format('d M') }}</div>
                                                        <div class="text-[8px] font-bold text-slate-400 uppercase">{{ $record->created_at->format('Y') }}</div>
                                                    </div>
                                                    
                                                    <div class="grow p-3 rounded-xl border bg-slate-50 border-slate-200">
                                                        <div class="flex justify-between items-center mb-1">
                                                            <span class="text-[9px] font-black uppercase {{ strtoupper($record->status) === 'REJECTED' ? 'text-rose-600' : 'text-emerald-600' }}">
                                                                {{ $record->status }}
                                                            </span>
                                                            <span class="font-mono text-[8px] text-slate-400">BATCH #{{ $record->submission_id }}</span>
                                                        </div>
                                                        <p class="text-xs text-slate-600 italic">"{{ $record->audit?->comments ?? ($record->remarks ?? 'No remarks.') }}"</p>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif(!$currentDecision)
                                    <div class="text-center py-10 opacity-40 font-black italic uppercase text-[10px]">No historical activity found.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection