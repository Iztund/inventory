@extends('layouts.auditor')

@section('title', 'Submission Details | ' . str_pad($submission->submission_id, 4, '0', STR_PAD_LEFT))

@section('content')
<div class="container-fluid px-4 py-6 min-vh-100 bg-gradient-to-br from-slate-50 to-slate-100">
    
    {{-- HEADER SECTION --}}
    <div class="mb-6">
        <div class="bg-white rounded-2xl shadow-lg border-l-8 border-indigo-600 overflow-hidden">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                    <div class="flex items-center gap-4">
                        <a href="{{ url()->previous() == url()->current() ? route('auditor.dashboard') : url()->previous() }}" 
                           class="w-14 h-14 bg-slate-900 text-white rounded-xl flex items-center justify-center shadow-lg hover:scale-105 transition-all text-decoration-none">
                            <i class="fas fa-arrow-left text-lg"></i>
                        </a>
                        <div>
                            <span class="text-[9px] font-black text-indigo-600 uppercase tracking-[0.3em] block mb-1">
                                College of Medicine Inventory
                            </span>
                            <h1 class="text-3xl font-black text-slate-900 uppercase tracking-tight m-0">
                                BATCH #{{ str_pad($submission->submission_id, 5, '0', STR_PAD_LEFT) }}
                            </h1>
                        </div>
                    </div>

                    <button onclick="window.print()" class="w-full lg:w-auto bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg transition-all">
                        <i class="fas fa-print me-2"></i> Print Record
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- MAIN CONTENT --}}
        <div class="col-lg-8">
            {{-- STATUS & METRICS DASHBOARD --}}
            <div class="row g-3 mb-5">
                {{-- STATUS CARD --}}
                <div class="col-md-4">
                    <div class="bg-white rounded-xl p-5 shadow-sm border-b-4 border-indigo-600 h-100 d-flex flex-column align-items-center justify-content-center text-center">
                        <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest d-block mb-3">
                            Current Status
                        </label>
                        @php
                            $statusConfig = match($submission->status) {
                                'approved' => ['bg' => 'bg-success', 'text' => 'text-white', 'label' => 'APPROVED'],
                                'rejected' => ['bg' => 'bg-danger', 'text' => 'text-white', 'label' => 'REJECTED'],
                                default => ['bg' => 'bg-warning', 'text' => 'text-dark', 'label' => 'PENDING']
                            };
                        @endphp
                        <span class="{{ $statusConfig['bg'] }} {{ $statusConfig['text'] }} px-5 py-2 rounded-pill font-black text-xs uppercase shadow-md d-inline-block">
                            {{ $statusConfig['label'] }}
                        </span>
                    </div>
                </div>
                
                {{-- VALUE CARD --}}
                <div class="col-md-8">
                    <div class="bg-gradient-to-r from-slate-900 to-indigo-900 rounded-xl shadow-xl h-100 overflow-hidden">
                        <div class="row g-0 h-100 items-center">
                            <div class="col-12 col-sm-7 p-4 p-lg-5 d-flex flex-column align-items-center justify-content-center text-center">
                                <label class="text-[10px] font-black text-indigo-200 uppercase tracking-[0.2em] mb-2">
                                    Total Batch Value
                                </label>
                                {{-- Reduced from 3xl/4xl to xl/2xl to ensure it fits on one line --}}
                                <h2 class="text-xl sm:text-2xl font-black text-white m-0 tracking-tighter whitespace-nowrap text-nowrap">
                                    <span class="text-base opacity-60 font-bold me-1">₦</span>{{ number_format($submission->items->sum(fn($i) => ($i->cost ?? 0) * $i->quantity), 2) }}
                                </h2>
                            </div>

                            <div class="col-12 col-sm-5 p-4 p-lg-5 d-flex align-items-center justify-content-center border-top border-sm-top-0 border-sm-start border-white border-opacity-10">
                                <div class="text-center">
                                    <div class="text-[10px] font-black text-indigo-100 uppercase tracking-[0.15em] mb-2 opacity-80">
                                        Submission Date
                                    </div>
                                    <div class="text-sm sm:text-base font-black text-white leading-tight whitespace-nowrap">
                                        {{ $submission->created_at->format('d M Y') }}
                                    </div>
                                    <div class="inline-block mt-2 px-2 py-1 bg-white bg-opacity-10 rounded text-[9px] font-bold text-indigo-100 uppercase tracking-tighter whitespace-nowrap">
                                        <i class="far fa-clock me-1 text-indigo-300"></i>
                                        {{ $submission->created_at->format('h:i A') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BATCH NOTES --}}
            @if($submission->note)
            <div class="bg-white rounded-2xl shadow-sm border-l-4 border-blue-500 p-5 mb-5">
                <h6 class="text-[9px] font-black text-blue-600 uppercase tracking-widest mb-3">
                    <i class="fas fa-sticky-note me-2"></i>Submission Remarks
                </h6>
                <p class="text-slate-800 font-bold italic text-base leading-relaxed m-0">
                    "{{ $submission->note }}"
                </p>
            </div>
            @endif

            {{-- AUDITOR VERDICT (if available) --}}
            @if($submission->status !== 'pending' && $submission->reviewedBy)
            <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-2xl border-2 border-indigo-200 p-6 mb-5 shadow-sm">
                <div class="d-flex flex-column flex-md-row align-items-start justify-content-between mb-4 gap-3">
                    <div class="flex-grow-1">
                        <h6 class="text-[9px] font-black text-indigo-600 uppercase tracking-widest mb-3">
                            Auditor Verdict
                        </h6>
                        <div class="d-flex align-items-center gap-3">
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; background: #4f46e5;">
                                <i class="fas fa-user-check text-white"></i>
                            </div>
                            <div>
                                <span class="d-block text-sm font-black text-slate-900">
                                    {{ $submission->reviewedBy->profile->full_name ?? $submission->reviewedBy->username }}
                                </span>
                                <span class="text-[9px] font-bold text-slate-500 uppercase">
                                    {{ $submission->updated_at->format('M d, Y - h:i A') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @if($submission->audits->first()?->comments)
                <div class="bg-white bg-opacity-70 rounded-xl p-4 border border-indigo-100">
                    <p class="text-slate-800 font-bold italic text-lg leading-relaxed m-0">
                        "{{ $submission->audits->first()->comments }}"
                    </p>
                </div>
                @endif
            </div>
            @endif

            {{-- ITEMS REGISTRY --}}
            <h5 class="font-black text-slate-900 uppercase tracking-tight mb-4 text-lg">
                <i class="fas fa-box-open me-2 text-indigo-600"></i>Asset Registry
            </h5>
            
            @foreach($submission->items as $index => $item)
                @php
                    $status = strtoupper($item->status ?? 'PENDING');
                    $statusColor = match($status) {
                        'APPROVED' => 'success',
                        'REJECTED' => 'danger',
                        default => 'warning'
                    };
                    $asset = $item->asset;
                    
                    // Handle document paths correctly
                    $docs = [];
                    if ($item->document_path) {
                        $docs = is_array($item->document_path) 
                            ? $item->document_path 
                            : json_decode($item->document_path, true) ?? [];
                    }
                @endphp

                <div class="bg-white rounded-2xl shadow-lg mb-5 overflow-hidden border border-slate-200">
                    {{-- Item Header --}}
                    <div class="bg-slate-900 text-white px-5 py-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                            <div class="flex-grow-1">
                                <h6 class="text-base font-black uppercase m-0 tracking-tight mb-2">
                                    ITEM #{{ $index + 1 }}: {{ $item->item_name }}
                                </h6>
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <span class="bg-white bg-opacity-10 px-3 py-1 rounded-lg text-[10px] font-black uppercase">
                                        QTY: {{ $item->quantity }}
                                    </span>
                                    <span class="bg-{{ $statusColor }} px-3 py-1 rounded-lg text-[10px] font-black uppercase text-white">
                                        {{ $status }}
                                    </span>
                                </div>
                            </div>
                            @if($asset)
                            <div class="text-start text-md-end">
                                <div class="text-[8px] font-bold text-slate-400 uppercase tracking-wider">Asset Tag</div>
                                <div class="font-mono font-black text-sm mt-1">{{ $asset->asset_tag ?? 'N/A' }}</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-6 p-md-6">
                        <div class="row g-4">
                            {{-- LEFT COLUMN: Details --}}
                            <div class="col-md-7 border-end border-slate-100 pe-md-4">
                                <div class="row g-3 mb-4">
                                    <div class="col-6 col-md-6">
                                        <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest d-block mb-2">
                                            Serial Number
                                        </label>
                                        <span class="font-mono font-bold text-slate-900 text-sm d-block">
                                            {{ $item->serial_number ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest d-block mb-2">
                                            Condition
                                        </label>
                                        <span class="badge bg-primary text-white text-xs font-black px-3 py-2">
                                            {{ $item->condition ?? 'Good' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-6 col-md-6">
                                        <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest d-block mb-2">
                                            Category
                                        </label>
                                        <span class="text-sm font-bold text-slate-900 d-block">
                                            {{ $item->category?->category_name ?? 'N/A' }}
                                        </span>
                                    </div>
                                    <div class="col-6 col-md-6">
                                        <label class="text-[9px] font-black text-slate-500 uppercase tracking-widest d-block mb-2">
                                            Sub-Category
                                        </label>
                                        <span class="text-sm font-bold text-slate-600 d-block">
                                            {{ $item->subcategory?->subcategory_name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Organizational Path --}}
                                @if($asset)
                                <div class="bg-slate-50 rounded-xl p-4 mb-4">
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest d-block mb-3">
                                        Organizational Path
                                    </label>
                                    <div class="d-flex flex-column gap-2">
                                        @foreach([
                                            ['Faculty', $asset->faculty?->faculty_name],
                                            ['Department', $asset->department?->dept_name],
                                            ['Office', $asset->office?->office_name],
                                            ['Unit', $asset->unit?->unit_name],
                                            ['Institute', $asset->institute?->institute_name]
                                        ] as [$label, $value])
                                            @if($value)
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-[9px] font-black text-slate-600 uppercase">{{ $label }}</span>
                                                <span class="text-xs font-bold text-slate-900">{{ $value }}</span>
                                            </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                {{-- Item Notes --}}
                                @if($item->item_note)
                                <div class="bg-warning bg-opacity-10 border border-warning rounded-xl p-4 mb-3">
                                    <label class="text-[9px] font-black text-warning uppercase tracking-widest d-block mb-2">
                                        Item Notes
                                    </label>
                                    <p class="text-sm text-slate-700 italic m-0">
                                        "{{ $item->item_note }}"
                                    </p>
                                </div>
                                @endif

                                {{-- Audit Comments for this specific item --}}
                                @if($item->audit?->comments)
                                <div class="bg-primary bg-opacity-10 border border-primary rounded-xl p-4">
                                    <label class="text-[9px] font-black text-primary uppercase tracking-widest d-block mb-2">
                                        <i class="fas fa-clipboard-check me-1"></i>Auditor Comments
                                    </label>
                                    <p class="text-sm text-slate-800 font-bold italic m-0">
                                        "{{ $item->audit->comments }}"
                                    </p>
                                </div>
                                @endif
                            </div>

                            {{-- RIGHT COLUMN: Documents & Value --}}
                            <div class="col-md-5 ps-md-4">
                                {{-- Financial Summary --}}
                                <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 rounded-xl p-4 sm:p-4 mb-3 shadow-lg border border-white/10">
                                    <div class="mb-3">
                                        <label class="text-[10px] font-black text-indigo-300 uppercase tracking-widest d-block mb-0.5 opacity-80">
                                            Unit Cost
                                        </label>
                                        {{-- Reduced from 2xl to base/lg to ensure a perfect fit --}}
                                        <div class="text-base sm:text-lg font-black text-white tracking-tighter leading-tight break-words">
                                            <span class="text-xs font-bold text-indigo-400">₦</span>{{ number_format($item->cost ?? 0, 2) }}
                                        </div>
                                    </div>

                                    <div class="border-t border-white/10 pt-3">
                                        <label class="text-[9px] font-black text-indigo-200 uppercase tracking-wider d-block mb-0.5 opacity-70">
                                            Subtotal Value
                                        </label>
                                        {{-- Compact sizing for the subtotal --}}
                                        <div class="text-sm sm:text-base font-black text-emerald-400 tracking-tighter leading-tight break-words">
                                            <span class="text-[10px] font-bold opacity-80">₦</span>{{ number_format(($item->cost ?? 0) * $item->quantity, 2) }}
                                        </div>
                                    </div>
                                </div>
                                {{-- Evidence Documents --}}
                                <label class="text-[9px] font-black text-slate-600 uppercase tracking-widest d-block mb-2">
                                    <i class="fas fa-paperclip me-1"></i>Evidence Documents
                                </label>

                                <div class="d-flex flex-column gap-1">
                                    @forelse($docs as $doc)
                                        @php 
                                            $cleanPath = str_replace('public/', '', $doc);
                                            $fileName = basename($cleanPath); 
                                            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                                            
                                            $icon = match(strtolower($extension)) {
                                                'pdf' => ['icon' => 'fa-file-pdf', 'color' => 'text-danger'],
                                                'jpg', 'jpeg', 'png', 'webp' => ['icon' => 'fa-file-image', 'color' => 'text-primary'],
                                                default => ['icon' => 'fa-file', 'color' => 'text-secondary']
                                            };
                                        @endphp
                                        
                                        <a href="{{ asset('storage/' . $cleanPath) }}" target="_blank" 
                                        class="d-flex align-items-center p-2 bg-white border border-slate-200 rounded-lg text-decoration-none hover:bg-slate-50 transition-all shadow-sm">
                                            
                                            {{-- Compact Icon Container --}}
                                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center bg-slate-100 rounded" style="width: 28px; height: 28px;">
                                                <i class="fas {{ $icon['icon'] }} {{ $icon['color'] }} text-[12px]"></i>
                                            </div>
                                            
                                            {{-- Text Area - Forced to a Single Line --}}
                                            <div class="ms-2 flex-grow-1 overflow-hidden">
                                                <p class="text-[10px] font-black text-slate-700 m-0 truncate" title="{{ $fileName }}">
                                                    {{ $fileName }}
                                                </p>
                                            </div>

                                            {{-- Small External Link --}}
                                            <div class="ms-2">
                                                <i class="fas fa-external-link-alt text-slate-300 text-[9px]"></i>
                                            </div>
                                        </a>
                                    @empty
                                        <div class="p-2 border border-dashed border-slate-200 rounded-lg text-center">
                                            <span class="text-[8px] font-black text-slate-400 uppercase tracking-widest italic">No files attached</span>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- SIDEBAR: Custodian Info --}}
        <div class="col-lg-4">
            <div class="position-sticky" style="top: 1.5rem;">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-slate-200">
                    <div class="bg-gradient-to-r from-slate-900 to-indigo-900 p-5 text-center">
                        <h6 class="text-[9px] font-black text-indigo-300 uppercase tracking-[0.3em] mb-4">
                            Reporting Officer
                        </h6>
                        <div class="mx-auto mb-4 d-flex align-items-center justify-content-center rounded-circle" style="width: 80px; height: 80px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                            <i class="fas fa-user-md text-white" style="font-size: 2rem;"></i>
                        </div>
                        <h6 class="text-lg font-black text-white mb-2 px-3">
                            {{ $submission->submittedBy->profile->full_name ?? ucwords(str_replace(['_', 'Staff'], [' ', ''], $submission->submittedBy->username)) }}
                        </h6>
                        <span class="text-[9px] font-bold text-indigo-200 uppercase tracking-widest d-block">
                            Inventory Personnel
                        </span>
                    </div>

                    <div class="p-5">
                        {{-- Division Origin --}}
                        <div class="mb-4 pb-4 border-bottom border-slate-100">
                            <label class="text-[8px] font-black text-slate-500 uppercase tracking-widest d-block mb-3">
                                Division Origin
                            </label>
                            @php
                                $u = $submission->submittedBy;
                                $origin = $u->office?->office_name 
                                        ?? $u->faculty?->faculty_name 
                                        ?? $u->institute?->institute_name 
                                        ?? 'College General';
                                
                                $subdivision = $u->unit?->unit_name 
                                             ?? $u->department?->dept_name 
                                             ?? null;
                            @endphp
                            <p class="text-sm font-black text-slate-900 mb-2 lh-sm">
                                {{ $origin }}
                            </p>
                            @if($subdivision)
                                <p class="text-xs font-bold text-primary mb-0 d-flex align-items-center">
                                    <i class="fas fa-level-down-alt fa-rotate-90 me-2 text-[10px]"></i>
                                    {{ $subdivision }}
                                </p>
                            @endif
                        </div>

                        {{-- Submission Timeline --}}
                        <div class="bg-light rounded-xl p-2">
                            <label class="text-[12px] font-black text-slate-700 uppercase tracking-widest d-block mb-3">
                                Timeline
                            </label>
                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-primary" style="width: 32px; height: 32px;">
                                        <i class="fas fa-upload text-white" style="font-size: 0.75rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-[9px] font-bold text-slate-700 uppercase mb-1">Submitted</div>
                                        <div class="text-xs font-black text-dark">
                                            {{ $submission->created_at->format('M d, Y h:i A') }}
                                        </div>
                                    </div>
                                </div>
                                @if($submission->updated_at != $submission->created_at)
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center bg-success" style="width: 32px; height: 32px;">
                                        <i class="fas fa-check text-white" style="font-size: 0.75rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-[9px] font-bold text-slate-700 uppercase mb-1">Last Updated</div>
                                        <div class="text-xs font-black text-dark">
                                            {{ $submission->updated_at->format('M d, Y h:i A') }}
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-bg-slate-100:hover {
    background-color: #f8f9fa !important;
}

.text-success {
    color: #10b981 !important;
}

@media print {
    body { background: white !important; }
    .container-fluid { background: white !important; padding: 0 !important; }
    .position-sticky { position: relative !important; }
}
</style>
@endsection