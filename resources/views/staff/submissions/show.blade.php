@extends('layouts.staff')

@section('title', 'Submission Details')

@section('content')

<div class="container-fluid px-3 px-lg-5 py-4" style="max-width: 1600px;">
    
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('staff.submissions.index') }}" 
               class="btn btn-white border border-slate-200 rounded-circle shadow-sm d-flex align-items-center justify-content-center"
               style="width:44px; height:44px; transition:all 0.2s;"
               onmouseenter="this.style.background='#0f172a'; this.style.borderColor='#0f172a'; this.querySelector('i').style.color='#fff';"
               onmouseleave="this.style.background='#fff'; this.style.borderColor='#e2e8f0'; this.querySelector('i').style.color='#94a3b8';">
                <i class="fas fa-arrow-left text-slate-400" style="font-size:0.85rem; transition:color 0.2s;"></i>
            </a>
            <div>
                <nav aria-label="breadcrumb" class="mb-1">
                    <ol class="breadcrumb mb-0" style="font-size:0.7rem;">
                        <li class="breadcrumb-item"><a href="{{ route('staff.dashboard') }}" class="text-decoration-none text-slate-500 fw-bold">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('staff.submissions.index') }}" class="text-decoration-none text-slate-500 fw-bold">Submissions</a></li>
                        <li class="breadcrumb-item active text-emerald-600 fw-bold">Details #{{ str_pad($submission->submission_id, 4, '0', STR_PAD_LEFT) }}</li>
                    </ol>
                </nav>
                <h1 class="fw-black text-slate-900 mb-0" style="font-size:1.5rem; letter-spacing:-0.02em;">Submission Details</h1>
            </div>
        </div>

        <div class="d-flex gap-2">
            @if($submission->status == 'pending')
            <a href="{{ route('staff.submissions.edit', $submission->submission_id) }}" 
               class="btn btn-sm btn-white border border-slate-200 rounded-2 fw-bold d-flex align-items-center gap-1"
               style="font-size:0.78rem; padding:0.5rem 1rem;">
                <i class="fas fa-edit text-slate-600"></i>
                <span class="d-none d-sm-inline">Edit</span>
            </a>
            @endif
            <button onclick="window.print()" 
                    class="btn btn-sm btn-white border border-slate-200 rounded-2 fw-bold d-flex align-items-center gap-1"
                    style="font-size:0.78rem; padding:0.5rem 1rem;">
                <i class="fas fa-print text-slate-600"></i>
                <span class="d-none d-sm-inline">Print</span>
            </button>
        </div>
    </div>

   {{-- Status Banner --}}
    @php
        $statusConfig = [
            'pending' => [
                'bg' => '#0f172a', 
                'accent' => '#059669', 
                'icon' => 'fa-clock-rotate-left',
                'text' => 'Pending Audit',
                'description' => 'Awaiting review by the College of Medicine audit team.'
            ],
            'approved' => [
                'bg' => '#064e3b', 
                'accent' => '#10b981', 
                'icon' => 'fa-check-double',
                'text' => 'Audit Cleared',
                'description' => 'Verification complete. Assets integrated into inventory.'
            ],
            'rejected' => [
                'bg' => '#450a0a', 
                'accent' => '#ef4444', 
                'icon' => 'fa-circle-exclamation',
                'text' => 'Discrepancy Found',
                'description' => 'Verification failed. Please review the notes for corrections.'
            ],
        ];
        $statusInfo = $statusConfig[$submission->status] ?? $statusConfig['pending'];
        
        $typeLabels = [
            'new_purchase' => 'New Procurement',
            'transfer'     => 'Asset Relocation',
            'disposal'     => 'Decommissioning',
            'maintenance'        => 'Maintenance/Repair'
        ];

        $generalNote = $submission->notes?? null;
    @endphp

    <div class="rounded-4 overflow-hidden mb-5 border-0 shadow-lg" 
         style="background: {{ $statusInfo['bg'] }}; position: relative;">
        
        <div class="p-4 p-md-5">
            <div class="row g-4 align-items-center">
                {{-- Left Content --}}
                <div class="col-lg-8">
                    <div class="d-flex align-items-start gap-4 mb-4">
                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                            style="width:64px; height:64px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);">
                            {{-- Added fa-beat class here --}}
                            <i class="fas {{ $statusInfo['icon'] }} fa-beat" style="color: {{ $statusInfo['accent'] }}; font-size:1.5rem; --fa-animation-duration: 2s;"></i>
                        </div>
                        <div>
                            <div class="d-flex flex-wrap gap-2 mb-2">
                                <span class="badge px-2 py-1 text-uppercase fw-black" 
                                      style="background: {{ $statusInfo['accent'] }}; font-size: 0.55rem; letter-spacing: 0.05em; color: #fff;">
                                    {{ $typeLabels[$submission->submission_type] ?? 'Inventory Entry' }}
                                </span>
                                <span class="text-white text-opacity-40 fw-bold font-monospace" style="font-size: 0.7rem; border: 1px solid rgba(255,255,255,0.1); padding: 2px 8px; border-radius: 4px;">
                                    #AUD-{{ str_pad($submission->submission_id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                            <h2 class="text-white fw-black mb-1" style="font-size:1.8rem; letter-spacing:-0.03em;">{{ $statusInfo['text'] }}</h2>
                            <p class="text-white text-opacity-80 mb-0" style="font-size: 0.9rem;">{{ $statusInfo['description'] }}</p>
                        </div>
                    </div>

                    {{-- General Note (Note logic integrated here) --}}
                    @if($generalNote)
                    <div class="mt-4 p-3 rounded-3" 
                        style="background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.05); max-width: 90%;">
                        <div class="d-flex align-items-center gap-2 mb-2 opacity-100">
                            <i class="fas fa-clipboard-list text-white" style="font-size: 0.7rem;"></i>
                            <span class="text-white text-uppercase fw-black" style="font-size: 0.6rem; letter-spacing: 0.1em;">
                                Staff Remarks & Justification for the whole Batch
                            </span>
                        </div>
                        <div style="max-height: 120px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.2) transparent;">
                            <p class="text-white text-opacity-90 mb-0" style="font-size: 0.88rem; line-height: 1.6; white-space: pre-line;">
                                {{ $generalNote }}
                            </p>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Right Content: Meta Info --}}
                <div class="col-lg-4">
                    <div class="d-flex flex-column gap-3 align-items-lg-end">
                        <div class="text-lg-end">
                            <label class="text-white opacity-80 text-uppercase fw-black d-block mb-1" style="font-size: 0.6rem; letter-spacing: 0.1em;">Submitting Location</label>
                            <p class="text-white fw-bold mb-0" style="font-size: 0.85rem;">{{ Auth::user()->unit->unit_name ?? Auth::user()->department->department_name ?? Auth::user()->institute->institute_name ?? 'College of Medicine' }}</p>
                        </div>
                        <div class="text-lg-end">
                            <label class="text-white opacity-80 text-uppercase fw-black d-block mb-1" style="font-size: 0.6rem; letter-spacing: 0.1em;">Timestamp</label>
                            <p class="text-white fw-bold mb-0" style="font-size: 0.85rem;">{{ $submission->created_at->format('D, M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Left Column: Items List --}}
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-black text-slate-900 mb-0" style="font-size:1.1rem;">Submission Items ({{ $submission->items->count() }})</h5>
                <span class="badge bg-slate-100 text-slate-700 fw-bold border border-slate-200" style="font-size:0.75rem; padding:0.4rem 0.9rem;">
                    Total: ₦{{ number_format($submission->total_value, 2) }}
                </span>
            </div>

            @foreach($submission->items as $index => $item)
            <div class="bg-white border border-slate-200 rounded-4 shadow-sm mb-4 overflow-hidden"
                 style="animation:fadeInUp 0.3s ease-out both; animation-delay:{{ $index * 0.05 }}s;">
                
                {{-- Item Header --}}
                <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100 d-flex justify-content-between align-items-center"
                     style="background:linear-gradient(to right, #f8fafc, #f1f5f9);">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-emerald-600 text-white d-flex align-items-center justify-content-center fw-black"
                             style="width:36px; height:36px; font-size:0.85rem;">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </div>
                        <div>
                            {{-- Logic: Maintenance/Retired show tags immediately; New Purchases show 'Pending' until approved --}}
@php 
    $tag = $item->generated_tag; 
@endphp

@if($tag === 'PENDING_ASSET_TAG')
    {{-- New Purchase: Waiting for Auditor --}}
    <code class="px-2 py-1 rounded-2 bg-slate-100 text-slate-500 fw-bold border border-slate-300 border-dashed d-inline-block mb-1" style="font-size:0.65rem;">
        <i class="fas fa-clock me-1"></i> AWAITING_APPROVAL
    </code>

@elseif($tag === 'ASSET_NOT_LINKED')
    {{-- Maintenance/Retired: Missing the Asset ID link --}}
    <code class="px-2 py-1 rounded-2 bg-amber-50 text-amber-700 fw-bold border border-amber-200 d-inline-block mb-1" style="font-size:0.65rem;">
        <i class="fas fa-exclamation-triangle me-1"></i> ASSET_TAG_MISSING
    </code>

@else
    {{-- Valid Tag: Show Emerald for New, Blue for Maintenance --}}
    <code class="px-2 py-1 rounded-2 {{ $submission->submission_type === 'new_purchase' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-blue-50 text-blue-700 border-blue-200' }} fw-black border d-inline-block mb-1 shadow-sm" 
          style="font-size:0.7rem;">
        <i class="fas {{ $submission->submission_type === 'new_purchase' ? 'fa-check-decagram' : 'fa-tools' }} me-1"></i>
        {{ $tag ?? 'Pending Tag' }}
    </code>
@endif

                            <h6 class="fw-bold text-slate-900 mb-0" style="font-size:0.95rem;">
                                {{ $item->item_name }}
                            </h6>

                            <p class="text-slate-500 mb-0 d-flex align-items-center gap-1" style="font-size:0.7rem;">
                                <span class="text-slate-700 fw-medium">{{ $item->category->category_name ?? 'Uncategorized' }}</span>
                                @if($item->subcategory)
                                    <i class="fas fa-chevron-right text-slate-300" style="font-size: 0.5rem;"></i>
                                    <span class="text-slate-400">{{ $item->subcategory->subcategory_name }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    @php
                        $itemStatusConfig = [
                            'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'fa-clock'],
                            'approved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'icon' => 'fa-check-circle'],
                            'rejected' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'icon' => 'fa-times-circle'],
                        ];
                        $itemStatus = $itemStatusConfig[$item->status ?? 'pending'] ?? $itemStatusConfig['pending'];
                    @endphp

                    <span class="badge rounded-pill fw-bold d-inline-flex align-items-center gap-1 border {{ $itemStatus['bg'] }} {{ $itemStatus['text'] }} {{ $itemStatus['border'] }}"
                          style="font-size:0.7rem; padding:0.4rem 0.85rem;">
                        <i class="fas {{ $itemStatus['icon'] }}" style="font-size:0.65rem;"></i>
                        {{ ucfirst($item->status ?? 'Pending') }}
                    </span>
                </div>
                
                <div class="p-3">
                    {{-- Metrics Row --}}
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-md-4">
                            <div class="text-center p-3 rounded-3 bg-slate-50 border border-slate-100">
                                <p class="text-slate-500 text-uppercase mb-1 fw-bold" style="font-size:0.65rem; letter-spacing:0.08em;">Quantity</p>
                                <p class="fw-black text-slate-900 mb-0" style="font-size:1.1rem;">{{ $item->quantity }}</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-center p-3 rounded-3 bg-slate-50 border border-slate-100">
                                <p class="text-slate-500 text-uppercase mb-1 fw-bold" style="font-size:0.65rem; letter-spacing:0.08em;">Unit Cost</p>
                                <p class="fw-black text-slate-900 mb-0" style="font-size:0.9rem;">₦{{ number_format($item->cost, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-6 col-md-4">
                            <div class="text-center p-3 rounded-3 bg-emerald-50 border border-emerald-100">
                                <p class="text-emerald-600 text-uppercase mb-1 fw-bold" style="font-size:0.65rem; letter-spacing:0.08em;">Total Value</p>
                                <p class="fw-black text-emerald-700 mb-0" style="font-size:1.0rem;">₦{{ number_format($item->cost * $item->quantity, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Additional Details --}}
                    @if($item->serial_number)
                    <div class="mb-3">
                        <label class="text-slate-500 text-uppercase mb-1 fw-bold d-block" style="font-size:0.65rem; letter-spacing:0.08em;">Serial Number</label>
                        <code class="px-2 py-1 rounded-2 bg-slate-100 text-slate-700 fw-bold border border-slate-200" style="font-size:0.8rem;">
                            {{ $item->serial_number }}
                        </code>
                    </div>
                    @endif

                    {{-- Item Notes --}}
                    @if($item->item_notes)
                    <div class="rounded-3 p-3 mb-4" style="background:#fffbeb; border-left:4px solid #f59e0b;">
                        <label class="text-amber-700 text-uppercase mb-2 fw-bold d-block" style="font-size:0.65rem; letter-spacing:0.08em;">
                            <i class="fas fa-sticky-note me-1"></i> Item Notes
                        </label>
                        <p class="text-slate-700 mb-0" style="font-size:0.82rem; line-height:1.6;">
                            {{ $item->item_notes }}
                        </p>
                    </div>
                    @endif
                    
                    {{-- Evidence & Attachments --}}
                    <div>
                        <label class="text-slate-700 text-uppercase mb-2 fw-bold d-block" style="font-size:0.7rem; letter-spacing:0.08em;">
                            <i class="fas fa-paperclip me-1"></i> Supporting Evidence
                        </label>
                        
                        <div class="row g-2">
                            {{-- $item->documents now returns a collection of objects via our new accessor --}}
                            @forelse($item->documents as $file)
                                <div class="col-md-6">
                                    <a href="{{ Storage::url($file->path) }}" target="_blank" 
                                    class="d-flex align-items-center justify-content-between p-3 bg-white border border-slate-200 rounded-3 text-decoration-none hover-emerald shadow-sm"
                                    style="transition: all 0.2s ease;">
                                        
                                        <div class="d-flex align-items-center gap-2 min-w-0">
                                            {{-- Determine icon based on the path extension --}}
                                            @if(str_ends_with(strtolower($file->path), '.pdf'))
                                                <i class="fas fa-file-pdf text-danger" style="font-size:1.3rem;"></i>
                                            @else
                                                <i class="fas fa-file-image text-emerald-600" style="font-size:1.3rem;"></i>
                                            @endif
                                            
                                            <div class="min-w-0">
                                                <div class="fw-bold text-slate-800 text-truncate" style="font-size:0.8rem;" title="{{ $file->name }}">
                                                    {{ $file->name }} {{-- This now displays the TRUE original name --}}
                                                </div>
                                                <div class="text-slate-500" style="font-size:0.68rem;">Click to view document</div>
                                            </div>
                                        </div>
                                        <i class="fas fa-external-link-alt text-slate-300" style="font-size:0.75rem;"></i>
                                    </a>
                                </div>
                            @empty
                                <div class="col-12">
                                    <div class="text-center py-4 rounded-3 bg-slate-50 border border-slate-200 border-dashed">
                                        <i class="fas fa-inbox text-slate-300 mb-2" style="font-size:1.5rem;"></i>
                                        <p class="text-slate-500 mb-0" style="font-size:0.75rem;">No attachments provided</p>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Right Column: Summary & Details --}}
        <div class="col-lg-4">
            <div class="sticky-top" style="top:85px;">
                
                {{-- Total Value Card --}}
                <div class="rounded-4 overflow-hidden shadow-lg mb-4"
                     style="background:linear-gradient(135deg, #059669 0%, #047857 100%);">
                    <div class="p-4 text-center">
                        <p class="text-emerald-100 text-uppercase mb-2 fw-black" style="font-size:0.7rem; letter-spacing:0.1em;">
                            Submission Value
                        </p>
                        <h2 class="text-white fw-black mb-0" style="font-size:1.5rem; letter-spacing:-0.02em;">
                            ₦{{ number_format($submission->total_value, 2) }}
                        </h2>
                        <p class="text-emerald-200 mb-0 mt-2" style="font-size:0.72rem;">
                            {{ $submission->items->count() }} {{ $submission->items->count() == 1 ? 'Item' : 'Items' }} Total
                        </p>
                    </div>
                </div>

                {{-- Submission Info Card --}}
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden mb-4">
                    <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                         style="background:linear-gradient(to right, #f8fafc, #f1f5f9);">
                        <h6 class="fw-black text-slate-900 mb-0" style="font-size:0.9rem;">Submission Details</h6>
                    </div>
                    <div class="p-4">
                        <div class="mb-3 pb-3 border-bottom border-slate-100">
                            <label class="text-slate-500 text-uppercase mb-2 fw-bold d-block" style="font-size:0.65rem; letter-spacing:0.08em;">
                                <i class="fas fa-calendar text-slate-400 me-1"></i> Submitted On
                            </label>
                            <p class="text-slate-900 fw-bold mb-0" style="font-size:0.85rem;">
                                {{ $submission->submitted_at ? $submission->submitted_at->format('M d, Y • h:i A') : $submission->created_at->format('M d, Y • h:i A') }}
                            </p>
                        </div>

                        @if($submission->notes)
                        <div class="mb-3 pb-3 border-bottom border-slate-100">
                            <label class="text-slate-500 text-uppercase mb-2 fw-bold d-block" style="font-size:0.65rem; letter-spacing:0.08em;">
                                <i class="fas fa-sticky-note text-slate-400 me-1"></i> General Notes
                            </label>
                            <p class="text-slate-700 mb-0" style="font-size:0.8rem; line-height:1.6;">
                                {{ $submission->notes }}
                            </p>
                        </div>
                        @endif

                        @if($submission->summary)
                        <div>
                            <label class="text-slate-500 text-uppercase mb-2 fw-bold d-block" style="font-size:0.65rem; letter-spacing:0.08em;">
                                <i class="fas fa-file-alt text-slate-400 me-1"></i> Executive Summary
                            </label>
                            <p class="text-slate-700 mb-0" style="font-size:0.8rem; line-height:1.6;">
                                {{ $submission->summary }}
                            </p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Originating Entity Card --}}
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                         style="background:linear-gradient(to right, #f8fafc, #f1f5f9);">
                        <h6 class="fw-black text-slate-900 mb-0" style="font-size:0.9rem;">Submitting Entity</h6>
                    </div>
                    <div class="p-4">
                        @php $u = Auth::user(); @endphp
                        @foreach(['faculty', 'institute', 'department', 'office', 'unit'] as $entity)
                            @if($u && $u->$entity)
                                <div class="d-flex align-items-start gap-3 mb-3 pb-3 {{ $loop->last ? '' : 'border-bottom border-slate-100' }}">
                                    <div class="rounded-3 bg-emerald-50 d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width:44px; height:44px;">
                                        <i class="fas fa-{{ $entity == 'faculty' ? 'graduation-cap' : ($entity == 'institute' ? 'university' : ($entity == 'department' ? 'building-columns' : ($entity == 'office' ? 'briefcase' : 'microscope'))) }} text-emerald-600" style="font-size:1.1rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="text-slate-500 text-uppercase mb-1 fw-bold" style="font-size:0.65rem; letter-spacing:0.08em;">{{ $entity }}</p>
                                        <p class="text-slate-900 fw-bold mb-0" style="font-size:0.88rem;">{{ $u->$entity->{$entity.'_name'} }}</p>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fadeInUp {
    from { opacity:0; transform:translateY(10px); }
    to { opacity:1; transform:translateY(0); }
}

@media print {
    .btn, nav, .sticky-top { display:none !important; }
    .bg-white { box-shadow:none !important; border:1px solid #e2e8f0 !important; }
}
</style>

@endsection