@extends('layouts.auditor')

@section('title', 'Audit Review | ' . str_pad($submission->submission_id, 4, '0', STR_PAD_LEFT))

@section('content')
<form id="auditForm" action="{{ route('auditor.submissions.store', $submission->submission_id) }}" method="POST">
    @csrf
    <input type="hidden" name="overall_decision" id="final_decision_input">

    <div class="container-fluid py-5 min-vh-100" style="background: linear-gradient(135deg, #f1f5f9 0%, #cbd5e1 100%);">
        
        {{-- TOP NAVIGATION --}}
        <div class="row mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white bg-opacity-75" style="backdrop-filter: blur(20px);">
                    <div class="card-body p-0">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center p-4 border-start border-5 border-dark">
                            <a href="javascript:window.history.back()" class="text-decoration-none d-flex align-items-center p-2 rounded-4 transition hover-bg-light">
                                <div class="bg-dark text-white rounded-circle me-4 d-flex align-items-center justify-content-center shadow-lg" style="width: 55px; height: 55px;">
                                    <i class="fas fa-chevron-left"></i>
                                </div>
                                <div>
                                    <span class="text-uppercase text-muted fw-bold mb-1 d-block" style="font-size: 0.7rem; letter-spacing: 1px;">College Inventory Audit</span>
                                    <h3 class="fw-bolder mb-0 text-dark font-monospace">BATCH #{{ str_pad($submission->submission_id, 5, '0', STR_PAD_LEFT) }}</h3>
                                </div>
                            </a>

                            <div class="d-flex gap-3 mt-3 mt-md-0">
                                <button type="button" class="btn btn-outline-danger px-4 rounded-pill fw-bold border-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#auditDecisionModal" onclick="prepareFinalDecision('rejected')">Reject Batch</button>
                                <button type="button" class="btn btn-dark px-5 rounded-pill fw-bold shadow-lg d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#auditDecisionModal" onclick="prepareFinalDecision('approved')">
                                    <span class="d-inline-block bg-success rounded-circle me-3 pulse-active" style="width: 12px; height: 12px;"></span>
                                    Finalize Approval
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-9">
                {{-- STATUS DASHBOARD --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card p-4 rounded-4 shadow-sm border-0 h-100 border-bottom border-warning border-4 bg-white">
                            <label class="text-dark fw-bold text-uppercase d-block mb-3" style="font-size: 0.75rem;">Audit Status</label>
                            <div class="d-inline-flex align-items-center bg-dark text-warning px-4 py-2 rounded-3 shadow-sm">
                                <div class="pulse-container me-3">
                                    <div class="pulse-ring-yellow"></div>
                                    <div class="pulse-dot-yellow"></div>
                                </div>
                                <span class="fw-bolder tracking-widest small">PENDING REVIEW</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card border-0 shadow-lg rounded-4 h-100 bg-white p-4 border-bottom border-success border-4">
                            <div class="row align-items-center">
                                <div class="col-sm-7">
                                    <label class="text-muted fw-bold text-uppercase d-block mb-1" style="font-size: 0.75rem;">Total Submission Value</label>
                                    <h2 class="fw-bolder mb-0 text-dark">
                                        @php
                                            // Handle case where $submission is an item or a batch
                                            $total = isset($submission->items) 
                                                ? $submission->items->sum(fn($i) => ($i->unit_cost ?? $i->cost ?? 0) * $i->quantity)
                                                : ($submission->unit_cost ?? $submission->cost ?? 0) * $submission->quantity;
                                        @endphp
                                        ₦ {{ number_format($total, 2) }}
                                    </h2>
                                </div>
                                <div class="col-sm-5 text-sm-end">
                                    <div class="bg-light border text-dark d-inline-block px-3 py-2 rounded-3 shadow-sm">
                                        <div class="x-small text-uppercase text-muted fw-bold">Submitted At</div>
                                        <span class="small fw-bold">{{ \Carbon\Carbon::parse($submission->submitted_at ?? $submission->submission->submitted_at ?? now())->format('d M Y, h:i A') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- GENERAL NOTES --}}
                <div class="card border-0 shadow-sm rounded-4 mb-5 bg-white">
                    <div class="card-body p-4">
                        <h6 class="text-uppercase fw-bold text-dark small mb-3"><i class="fas fa-sticky-note me-2 text-primary"></i>General Submission Notes</h6>
                        <div class="p-4 rounded-4 bg-light border-start border-5 border-primary shadow-sm">
                            <p class="text-dark mb-0 fs-6 italic">"{{ $submission->notes ?? $submission->submission->notes ?? 'No administrative notes were provided.' }}"</p>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bolder text-dark mb-4 text-uppercase px-2" style="letter-spacing: 1px;">Itemized List</h5>
                
                @php
                    // Ensure we have a loopable collection
                    $itemsToLoop = isset($submission->items) ? $submission->items : [$submission];
                @endphp

                @foreach($itemsToLoop as $index => $item)
                    @php
                        $rawDocs = $item->evidence_file ?? $item->document_path;
                        $docs = is_array($rawDocs) ? $rawDocs : json_decode($rawDocs, true) ?? [];
                    @endphp

                    <input type="hidden" name="items[{{ $item->submission_item_id }}][status]" id="input-status-{{ $item->submission_item_id }}" value="pending">

                    <div class="card border-0 shadow-lg rounded-4 mb-5 overflow-hidden bg-white item-row" id="item-row-{{ $item->submission_item_id }}">
                        <div class="bg-dark text-white px-4 py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">ITEM #{{ $index + 1 }}: {{ strtoupper($item->item_name) }}</h6>
                            <span class="badge bg-success px-3">QTY: {{ $item->quantity }}</span>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-8 border-end">
                                    {{-- PRIMARY DETAILS --}}
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-5">
                                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-1">Acquisition Type</label>
                                            @php
                                                $type = strtolower($item->submission->submission_type ?? 'new_purchase');
                                                $typeLabel = str_replace('_', ' ', $type);
                                                $badgeClass = match($type) {
                                                    'transfer' => 'bg-warning-subtle text-warning border-warning',
                                                    'donation' => 'bg-purple-subtle text-purple border-purple',
                                                    default    => 'bg-success-subtle text-success border-success',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} border px-3 text-uppercase" style="font-size: 0.7rem;">
                                                {{ $typeLabel }}
                                            </span>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-1">Serial Number</label>
                                            <span class="fw-bold font-monospace text-dark small">{{ $item->serial_number ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-1">Unit Cost</label>
                                            <span class="fw-bold text-dark">₦{{ number_format($item->unit_cost ?? $item->cost ?? 0, 2) }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-1">Condition</label>
                                            <span class="badge bg-info-subtle text-info border border-info px-3">{{ strtoupper($item->condition ?? 'Good') }}</span>
                                        </div>
                                    </div>

                                    {{-- EVIDENCE SECTION --}}
                                    <div class="mb-4">
                                        <label class="text-muted fw-bold text-uppercase x-small d-block mb-3 italic border-bottom pb-1">Attached Evidence & Documents</label>
                                        <div class="row g-2">
                                            @forelse($docs as $doc)
                                                @php 
                                                    $cleanPath = str_replace('public/', '', $doc); 
                                                    $isPdf = Str::endsWith(strtolower($doc), '.pdf');
                                                @endphp
                                                <div class="col-sm-6">
                                                    <a href="{{ asset('storage/' . $cleanPath) }}" target="_blank" class="d-flex align-items-center p-2 bg-light border rounded-3 text-decoration-none transition hover-bg-white group">
                                                        <div class="bg-white rounded border d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                                            <i class="fas {{ $isPdf ? 'fa-file-pdf text-danger' : 'fa-file-image text-primary' }}"></i>
                                                        </div>
                                                        <div class="ms-3 overflow-hidden">
                                                            <p class="mb-0 x-small fw-bold text-dark text-truncate">VIEW_DOC_{{ $loop->iteration }}</p>
                                                            <span class="x-small text-muted text-uppercase" style="font-size: 0.55rem;">Click to Open</span>
                                                        </div>
                                                    </a>
                                                </div>
                                            @empty
                                                <div class="col-12 text-center py-3 border-2 border-dashed rounded-4 bg-light">
                                                    <span class="x-small fw-bold text-muted text-uppercase">No Documents Attached</span>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <label class="text-muted fw-bold text-uppercase x-small d-block mb-2">Item Internal Notes</label>
                                    <p class="small bg-light p-3 rounded border mb-0 italic">"{{ $item->item_notes ?? 'No item-specific notes provided.' }}"</p>
                                </div>

                                {{-- ACTION SIDE --}}
                                <div class="col-md-4 text-center d-flex flex-column justify-content-center">
                                    <label class="text-muted fw-bold text-uppercase x-small mb-1">Calculated Value</label>
                                    <h4 class="fw-bolder text-dark mb-4">₦{{ number_format(($item->unit_cost ?? $item->cost ?? 0) * $item->quantity, 2) }}</h4>

                                    <div class="btn-group w-100 shadow-sm border rounded-pill overflow-hidden bg-white">
                                        <button type="button" onclick="markItem({{ $item->submission_item_id }}, 'rejected')" class="btn btn-white text-danger fw-bold border-end" id="rej-{{ $item->submission_item_id }}">Reject</button>
                                        <button type="button" onclick="markItem({{ $item->submission_item_id }}, 'approved')" class="btn btn-white text-success fw-bold" id="app-{{ $item->submission_item_id }}">Approve</button>
                                    </div>
                                    <small id="status-label-{{ $item->submission_item_id }}" class="mt-3 text-muted x-small italic fw-bold">PENDING AUDIT</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- SIDEBAR --}}
            <div class="col-lg-3">
                <div class="sticky-top" style="top: 2rem;">
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4">
                        <div class="bg-dark p-3 text-center">
                            <h6 class="text-white-50 text-uppercase fw-bold mb-0 x-small tracking-widest">Reporting Officer</h6>
                        </div>
                        <div class="p-4 text-center">
                            <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px;">
                                <i class="fas fa-user-md fa-2x text-dark"></i>
                            </div>
                            
                            @php $submitter = $submission->submittedBy ?? $submission->submission->submittedBy ?? null; @endphp
                            <h6 class="fw-bolder text-dark mb-0">
                                {{ $submitter ? ucwords(str_replace(['_', 'Staff'], [' ', ''], $submitter->full_name)) : 'System User' }}
                            </h6>
                            <div class="d-flex align-items-center gap-1">
                                <i class="fas fa-envelope text-muted" style="font-size: 0.65rem;"></i>
                                <span class="text-muted small italic" style="font-size: 0.75rem;">
                                    {{ $submitter->email ?? 'no-reply@college.edu' }}
                                </span>
                            </div>


                            <span class="x-small text-uppercase fw-bold text-primary d-block mb-3">
                                @if($submitter && $submitter->unit) {{ $submitter->unit->unit_name }}
                                @elseif($submitter && $submitter->department) {{ $submitter->department->dept_name }}
                                @elseif($submitter && $submitter->institute) {{ $submitter->institute->institute_name }}
                                @else General Staff @endif
                            </span>
                            
                            <div class="text-start border-top pt-3 mt-3">
                                <label class="x-small text-muted fw-bold text-uppercase d-block mb-1">Affiliation Hierarchy</label>
                                <p class="small fw-bold text-dark mb-0">
                                    @if($submitter && $submitter->unit && $submitter->unit->office)
                                        <span class="text-muted fw-normal x-small d-block">Office:</span>
                                        {{ $submitter->unit->office->office_name }}
                                    @elseif($submitter && $submitter->department && $submitter->department->faculty)
                                        <span class="text-muted fw-normal x-small d-block">Faculty:</span>
                                        {{ $submitter->department->faculty->faculty_name }}
                                    @elseif($submitter && $submitter->office)
                                        <span class="text-muted fw-normal x-small d-block">Main Office:</span>
                                        {{ $submitter->office->office_name }}
                                    @else
                                        <span class="text-muted fw-normal x-small d-block">Division:</span>
                                        College of Medicine
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow rounded-4 bg-dark p-4">
                        <p class="x-small text-white-50 fw-bold mb-0 italic">
                            <strong>Note:</strong> Items approved here are officially entered into the College's digital ledger.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL --}}
    <div class="modal fade" id="auditDecisionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body p-5 text-center">
                    <div id="modal_icon_container" class="mb-4"></div>
                    <h3 class="fw-bolder text-dark" id="modal_title">Decision</h3>
                    <div class="text-start mt-4">
                        <label class="x-small fw-bold text-uppercase text-muted mb-2">Auditor's Final Remarks</label>
                        <textarea name="comments" class="form-control border shadow-sm bg-light" rows="3" placeholder="Enter reason..." required></textarea>
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-dark btn-lg rounded-pill fw-bold" id="modal_submit_btn">Confirm Action</button>
                        <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Back</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    .transition { transition: all 0.2s ease-in-out; }
    .pulse-container { position: relative; width: 12px; height: 12px; display: flex; align-items: center; justify-content: center; }
    .pulse-dot-yellow { width: 10px; height: 10px; background: #ffc107; border-radius: 50%; z-index: 2; }
    .pulse-ring-yellow { position: absolute; width: 25px; height: 25px; border: 3px solid #ffc107; border-radius: 50%; animation: pulse-ring 1.5s infinite; }
    @keyframes pulse-ring { 0% { transform: scale(0.33); opacity: 1; } 80%, 100% { transform: scale(1.2); opacity: 0; } }
    .x-small { font-size: 0.65rem; }
    .item-row { transition: 0.3s; border-left: 0px solid transparent; }
    .item-verified { border-left: 10px solid #198754 !important; background-color: #f8fff9 !important; }
    .item-flagged { border-left: 10px solid #dc3545 !important; background-color: #fff8f8 !important; }
</style>

<script>
    function markItem(itemId, status) {
        const row = document.getElementById(`item-row-${itemId}`);
        const label = document.getElementById(`status-label-${itemId}`);
        const input = document.getElementById(`input-status-${itemId}`);
        const app = document.getElementById(`app-${itemId}`);
        const rej = document.getElementById(`rej-${itemId}`);

        input.value = status;
        row.classList.remove('item-verified', 'item-flagged');

        if(status === 'approved') {
            row.classList.add('item-verified');
            app.className = "btn bg-success text-white fw-bold";
            rej.className = "btn btn-white text-danger fw-bold border-end";
            label.innerText = "VERIFIED / ACCURATE";
            label.className = "mt-3 text-success fw-bold x-small italic";
        } else {
            row.classList.add('item-flagged');
            rej.className = "btn bg-danger text-white fw-bold border-end";
            app.className = "btn btn-white text-success fw-bold";
            label.innerText = "FLAGGED FOR REJECTION";
            label.className = "mt-3 text-danger fw-bold x-small italic";
        }
    }

    function prepareFinalDecision(decision) {
        document.getElementById('final_decision_input').value = decision;
        const title = document.getElementById('modal_title');
        const icon = document.getElementById('modal_icon_container');
        const btn = document.getElementById('modal_submit_btn');

        if(decision === 'approved') {
            title.innerText = "Confirm Batch Approval";
            icon.innerHTML = '<i class="fas fa-check-double fa-4x text-success"></i>';
            btn.className = "btn btn-success btn-lg rounded-pill fw-bold";
        } else {
            title.innerText = "Reject Batch & Send Feedback";
            icon.innerHTML = '<i class="fas fa-exclamation-triangle fa-4x text-danger"></i>';
            btn.className = "btn btn-danger btn-lg rounded-pill fw-bold";
        }
    }
</script>
@endsection