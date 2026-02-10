@extends('layouts.auditor')

@section('content')
<div class="container-fluid py-5 bg-slate-50 min-vh-100">
    
    {{-- 1. HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4 px-4">
        <div class="flex items-center">
            <a href="{{ url()->previous() == url()->current() ? route('auditor.dashboard') : url()->previous() }}" 
               class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-slate-200 text-slate-500 hover:scale-110 transition-all text-decoration-none">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="ms-4">
                <h3 class="font-black text-slate-800 m-0 text-2xl uppercase tracking-tighter italic">Verification Details</h3>
                <div class="text-[10px] font-black text-slate-800 uppercase tracking-widest mt-1">
                    Ref ID: #{{ str_pad($submission?->submission_id ?? 'N/A', 7, '0', STR_PAD_LEFT) }} 
                    &bullet; {{ $submission?->items?->count() ?? 1 }} Item(s)
                </div>
            </div>
        </div>
        
        {{-- MODERNIZED REPORTING OFFICER BADGE --}}
        <div class="flex items-center gap-4 bg-white/80 backdrop-blur-md p-2 pe-4 rounded-full shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-200/50 group hover:border-blue-400 transition-all duration-500">
            <div class="relative">
                <div class="w-12 h-12 rounded-full bg-slate-900 flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-transform duration-500">
                    <i class="fas fa-user-md text-sm"></i>
                </div>
                <div class="absolute bottom-0 right-0 w-3.5 h-3.5 bg-emerald-500 border-2 border-white rounded-full"></div>
            </div>

            <div class="flex flex-col">
                <span class="text-[9px] font-black text-blue-600 uppercase tracking-[0.15em] leading-none mb-1">
                    Reporting Officer
                </span>
                <span class="text-[13px] font-black text-slate-800 uppercase italic tracking-tighter">
                    {{ $submission?->submittedBy?->profile?->full_name ?? $submission?->submittedBy?->username ?? 'System User' }}
                </span>
            </div>
        </div>
    </div>

    {{-- 2. GENERAL BATCH NOTE --}}
    <div class="mx-4 mb-8">
        <div class="p-5 bg-white border-l-4 border-blue-600 rounded-r-2xl shadow-sm">
            <h6 class="font-black text-slate-700 uppercase text-[10px] tracking-widest mb-1 italic">General Batch Note:</h6>
            <p class="text-slate-800 font-bold italic m-0 text-lg leading-relaxed">
                "{{ $submission?->note ?? 'No general remarks provided for this batch.' }}"
            </p>
        </div>
    </div>

    {{-- 3. ITEMS LOOP - FULL BATCH --}}
    @forelse($submission?->items ?? [] as $item)
        @php
            $status = strtoupper($item->status ?? 'PENDING');
            $themeColor = ($status === 'APPROVED') ? 'emerald' : (($status === 'REJECTED') ? 'rose' : 'amber');
            $asset = $item?->asset;
            $docs = is_array($item->document_path) ? $item->document_path : json_decode($item->document_path, true) ?? [];
        @endphp

        <div class="row g-4 mb-16 px-3">
            {{-- SIDEBAR --}}
            <div class="col-lg-4">
                <div class="bg-white rounded-[2rem] shadow-xl border border-slate-100 overflow-hidden sticky-top" style="top: 20px;">
                    <div class="bg-slate-900 p-6">
                        <!-- Inside the bg-slate-900 p-6 section -->
                        <div class="flex items-center gap-3 mb-2 w-full max-w-full overflow-hidden">
    
                            <div class="flex items-center gap-1.5 flex-shrink-0">
                                <span class="text-slate-200 font-black text-[10px] md:text-[11px] uppercase tracking-wider">Qty</span>
                                <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded-md font-black text-[11px] tracking-normal">
                                    {{ $item->quantity ?? 1 }}
                                </span>
                            </div>

                            <span class="w-[1px] h-3 bg-slate-700 flex-shrink-0"></span>

                            <div class="flex items-center gap-1.5 min-w-0 flex-1 overflow-hidden">
                                <span class="w-2 h-2 rounded-full bg-{{ $themeColor }}-500 animate-pulse flex-shrink-0"></span>
                                <span class="text-{{ $themeColor }}-400 font-black text-[10px] md:text-[11px] uppercase tracking-[0.1em] truncate block">
                                    {{ $status }}
                                </span>
                            </div>
                        </div>
                        <h4 class="text-white font-black text-2xl m-0 uppercase italic leading-tight">
                            {{ $item->item_name ?? 'Unnamed Item' }}
                        </h4>
                        <p class="text-slate-200 font-mono text-[11px] font-black mt-2 uppercase mb-0">
                            Asset Tag: {{ $asset?->asset_tag ?? 'NEW_ENTRY' }}
                        </p>
                    </div>

                    <div class="p-6">
                        <h6 class="text-[9px] font-black text-slate-700 uppercase tracking-widest mb-4 italic">Organizational Path</h6>
                        <div class="space-y-3 mb-6">
                            @php
                                $paths = [
                                    ['L' => 'Faculty', 'V' => $asset?->faculty?->faculty_name],
                                    ['L' => 'Department', 'V' => $asset?->department?->dept_name],
                                    ['L' => 'Office', 'V' => $asset?->office?->office_name],
                                    ['L' => 'Unit', 'V' => $asset?->unit?->unit_name],
                                    ['L' => 'Institute', 'V' => $asset?->institute?->institute_name]
                                ];
                            @endphp
                            @foreach($paths as $path)
                                @if($path['V'])
                                    <div class="flex justify-between items-center border-b border-slate-50 pb-2">
                                        <span class="text-[8px] font-black text-slate-700 uppercase">{{ $path['L'] }}</span>
                                        <span class="text-[11px] font-black text-slate-700 uppercase italic text-end">{{ $path['V'] }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl">
                            <span class="text-[9px] font-black text-amber-600 uppercase tracking-widest block mb-1 italic">Item Submission Note</span>
                            <p class="text-[12px] text-slate-600 font-bold italic leading-relaxed m-0">
                                "{{ $item->item_note ?? 'No specific notes for this item.' }}"
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MAIN CONTENT --}}
            <div class="col-lg-8">
                {{-- AUDITOR VERDICT --}}
                <div class="bg-white rounded-[2rem] p-7 mb-4 border-2 border-{{ $themeColor }}-600 shadow-sm overflow-visible">
                    <div class="flex flex-wrap justify-between items-start gap-4">
                        <div class="flex-1">
                            <h6 class="font-black text-slate-700 uppercase text-[10px] tracking-widest m-0 italic">Audit Verdict</h6>
                            <span class="text-[10px] font-black text-{{ $themeColor }}-700 bg-{{ $themeColor }}-50 px-3 py-1.5 rounded-xl uppercase inline-block mt-2">
                                Auditor: {{ $item->submission?->reviewedBy?->profile?->full_name ?? $item->submission?->reviewedBy?->username ?? 'Staff Auditor' }}
                            </span>
                        </div>
                        
                        <button type="button" 
                                data-bs-toggle="modal" 
                                data-bs-target="#reEvalModal{{ $item->submission_item_id }}"
                                class="bg-slate-900 hover:bg-black text-white px-5 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest transition-all flex items-center shadow-xl border-0 relative z-30">
                            <i class="fas fa-sync-alt me-2 text-blue-400"></i> Re-evaluate
                        </button>
                    </div>
                    <p class="text-slate-800 font-black text-2xl italic m-0 tracking-tight mt-4 leading-snug">
                        "{{ $item->audit?->comments ?? 'Physical state verified.' }}"
                    </p>
                </div>

                {{-- TABS --}}
                <div class="bg-white p-2 rounded-2xl shadow-sm mb-4 border border-slate-200">
                    <ul class="nav nav-pills nav-justified gap-2" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active rounded-xl py-3 font-black uppercase text-[11px] tracking-widest" data-bs-toggle="tab" data-bs-target="#specs-{{ $item->submission_item_id }}">Asset Details & Evidence</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-xl py-3 font-black uppercase text-[11px] tracking-widest" data-bs-toggle="tab" data-bs-target="#trail-{{ $item->submission_item_id }}">Audit Trail</button>
                        </li>
                    </ul>
                </div>

                <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 p-8">
                    <div class="tab-content">
                        {{-- TAB 1: SPECS --}}
                        <div class="tab-pane fade show active" id="specs-{{ $item->submission_item_id }}">
                            <div class="row g-4 mb-6">
                                <div class="col-md-4">
                                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 h-100">
                                        <small class="text-slate-400 font-black text-[9px] uppercase block mb-2 tracking-widest italic">Serial Number</small>
                                        <span class="text-slate-800 font-black font-mono text-sm block uppercase">{{ $item->serial_number ?: 'NOT_SPECIFIED' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 h-100">
                                        <small class="text-slate-400 font-black text-[9px] uppercase block mb-2 tracking-widest italic">Category</small>
                                        <span class="text-slate-800 font-black text-sm block uppercase leading-tight">{{ $item->category?->category_name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 h-100">
                                        <small class="text-slate-400 font-black text-[9px] uppercase block mb-2 tracking-widest italic">Sub-Category</small>
                                        <span class="text-slate-600 font-black text-sm block uppercase leading-tight">{{ $item->subcategory?->subcategory_name ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4 mb-8">
                                <div class="col-md-6">
                                    <div class="p-4 bg-slate-900 rounded-2xl text-white h-100 shadow-md">
                                        <small class="text-slate-500 font-black text-[9px] uppercase block mb-2 tracking-widest italic">Unit Acquisition Cost</small>
                                        <span class="font-black text-xl text-emerald-400 block">
                                            ₦{{ number_format($item->cost ?? 0, 2) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100 h-100 shadow-sm">
                                        <small class="text-emerald-600 font-black text-[9px] uppercase block mb-2 tracking-widest italic">Calculated Value</small>
                                        <span class="text-emerald-900 font-black text-xl block">
                                            ₦{{ number_format(($item->cost ?? 0) * ($item->quantity ?? 1), 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="font-black text-slate-800 uppercase text-[10px] tracking-widest mb-4 italic border-b pb-2">Verification Evidence</h6>
                            <div class="row g-3">
                                @forelse($docs as $doc)
                                    <div class="col-md-6">
                                        <a href="{{ asset('storage/' . $doc) }}" target="_blank" class="flex items-center p-4 bg-slate-50 border border-slate-200 rounded-2xl hover:bg-white transition-all text-decoration-none group">
                                            <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm">
                                                <i class="fas {{ Str::endsWith($doc, '.pdf') ? 'fa-file-pdf text-red-500' : 'fa-file-image text-blue-500' }} text-lg"></i>
                                            </div>
                                            <div class="ms-3">
                                                <p class="m-0 text-[11px] font-black text-slate-700 uppercase">Evidence_File_{{ $loop->iteration }}</p>
                                                <small class="text-[9px] text-slate-400 font-bold">CLICK TO VIEW SOURCE</small>
                                            </div>
                                        </a>
                                    </div>
                                @empty
                                    <div class="col-12 py-8 text-center border-2 border-dashed border-slate-100 rounded-[2rem]">
                                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest m-0 italic">No evidence uploaded for this item.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- TAB 2: AUDIT TRAIL --}}
                        <div class="tab-pane fade" id="trail-{{ $item->submission_item_id }}">
                            <div class="relative space-y-6 ps-4 border-l-2 border-slate-100 ms-2">
                                @php
                                    $assetHistory = $history->where('submission_item_id', $item->submission_item_id)->sortByDesc('created_at');
                                @endphp
                                @forelse($assetHistory as $log)
                                    @if($loop->first)
                                        <div class="relative flex gap-4 items-start pb-2">
                                            <div class="absolute -left-[31px] w-7 h-7 rounded-full bg-blue-600 border-4 border-white shadow-sm flex items-center justify-center">
                                                <i class="fas fa-check text-[8px] text-white"></i>
                                            </div>
                                            <div class="grow bg-blue-50 p-5 rounded-[2rem] border border-blue-100 shadow-md">
                                                <div class="flex justify-between items-center mb-3">
                                                    <span class="bg-blue-600 text-white text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-tighter italic">Active Verdict</span>
                                                    <span class="text-[10px] font-black text-blue-400">{{ $log->created_at->format('M d, Y - H:i') }}</span>
                                                </div>
                                                <p class="text-[14px] font-black italic text-slate-800 mb-0 leading-relaxed bg-white/50 p-3 rounded-xl border border-blue-100">
                                                    "{{ $log->comments ?? 'No comments' }}"
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="relative flex gap-4 items-start pb-4 opacity-70">
                                            <div class="absolute -left-[23px] w-3 h-3 rounded-full bg-slate-300 border-2 border-white mt-2"></div>
                                            <div class="grow bg-slate-50 p-4 rounded-2xl border border-slate-200">
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-[9px] font-black uppercase text-slate-400 tracking-wider">Historical: {{ strtoupper($log->status ?? 'PENDING') }}</span>
                                                    <span class="text-[9px] font-bold text-slate-400">{{ $log->created_at->format('M d, Y') }}</span>
                                                </div>
                                                <p class="text-[11px] font-bold italic text-slate-600 mb-0 leading-relaxed">"{{ $log->comments ?? 'No previous comments' }}"</p>
                                            </div>
                                        </div>
                                    @endif
                                @empty
                                    <div class="p-6 text-center text-slate-500 italic">
                                        No audit trail entries yet.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- RE-EVALUATION MODAL --}}
        <div class="modal fade" id="reEvalModal{{ $item->submission_item_id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-[2.5rem] border-0 shadow-2xl overflow-hidden">
                    <form action="{{ route('auditor.submissions.re-evaluate', $submission?->submission_id ?? '') }}" method="POST">
                        @csrf
                        <input type="hidden" name="item_id" value="{{ $item->submission_item_id }}">
                        
                        <div class="bg-slate-900 p-6 text-center">
                            <h5 class="text-white font-black text-xl uppercase italic tracking-tighter m-0">Re-evaluate</h5>
                            <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest mt-2">{{ $item->item_name }}</p>
                        </div>
                        
                        <div class="modal-body p-6 bg-white">
                            <div class="mb-5">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Final Status Decision</label>
                                
                                <div class="dropdown">
                                    <button class="w-full bg-slate-50 border-2 border-slate-100 rounded-full py-3 px-5 flex justify-between items-center dropdown-toggle font-black text-[11px] uppercase tracking-wider" 
                                            type="button" id="statusDrop-{{ $item->submission_item_id }}" data-bs-toggle="dropdown" aria-expanded="false">
                                        <span id="selectedLabel-{{ $item->submission_item_id }}">{{ $status === 'REJECTED' ? 'Reject' : 'Approve' }}</span>
                                    </button>

                                    <ul class="dropdown-menu w-full rounded-[2rem] border-0 shadow-xl p-2 mt-2" aria-labelledby="statusDrop-{{ $item->submission_item_id }}">
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2xl py-3 font-black text-[10px] uppercase hover:bg-emerald-50 hover:text-emerald-700 transition-all" 
                                                    onclick="updateStatus('approved', 'Approve', {{ $item->submission_item_id }})">
                                                <i class="fas fa-check-circle me-2 text-emerald-500"></i> Approve
                                            </button>
                                        </li>
                                        <li>
                                            <button type="button" class="dropdown-item rounded-2xl py-3 font-black text-[10px] uppercase hover:bg-rose-50 hover:text-rose-700 transition-all" 
                                                    onclick="updateStatus('rejected', 'Reject', {{ $item->submission_item_id }})">
                                                <i class="fas fa-times-circle me-2 text-rose-500"></i> Reject
                                            </button>
                                        </li>
                                    </ul>

                                    <input type="hidden" name="new_status" id="hiddenStatus-{{ $item->submission_item_id }}" value="{{ strtolower($status) }}">
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Correction Justification (Min. 10 chars)</label>
                                <textarea name="correction_remarks" rows="5" class="form-control border-2 border-slate-100 rounded-[1.5rem] font-bold text-sm p-4 shadow-sm focus:border-blue-500" placeholder="Clearly state why the previous decision is being changed..." required></textarea>
                            </div>
                        </div>

                        <div class="p-6 pt-0 bg-white flex gap-3">
                            <button type="button" class="w-1/2 bg-slate-100 text-slate-500 py-4 rounded-2xl font-black text-[11px] uppercase border-0" data-bs-dismiss="modal">Discard</button>
                            <button type="submit" class="w-1/2 bg-blue-600 text-white py-4 rounded-2xl font-black text-[11px] uppercase border-0 shadow-lg shadow-blue-200 transition-all hover:scale-105">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center py-20 text-slate-500 font-bold">
            No items found in this batch.
        </div>
    @endforelse
</div>

<script>
    function updateStatus(val, label, id) {
        document.getElementById('hiddenStatus-' + id).value = val;
        document.getElementById('selectedLabel-' + id).innerText = label;
    }
</script>
@endsection