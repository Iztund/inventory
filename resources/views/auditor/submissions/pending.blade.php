@extends('layouts.auditor')

@section('title', 'Audit Review | ' . str_pad($submission->submission_id, 4, '0', STR_PAD_LEFT))

@section('content')
{{-- Wrap the entire content in a single form to capture all item decisions --}}
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
                                {{-- We now use type="button" to trigger modal, not direct submit --}}
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
                {{-- ... (Status & Value Dashboard same as before) ... --}}
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
                                    <h2 class="fw-bolder mb-0 text-dark">₦ {{ number_format($submission->items->sum(fn($i) => ($i->unit_cost ?? $i->cost ?? 0) * $i->quantity), 2) }}</h2>
                                </div>
                                <div class="col-sm-5 text-sm-end">
                                    <div class="bg-light border text-dark d-inline-block px-3 py-2 rounded-3 shadow-sm">
                                        <div class="x-small text-uppercase text-muted fw-bold">Submitted At</div>
                                        <span class="small fw-bold">{{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, h:i A') }}</span>
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
                            <p class="text-dark mb-0 fs-6 italic">"{{ $submission->notes ?? 'No administrative notes were provided.' }}"</p>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bolder text-dark mb-4 text-uppercase px-2" style="letter-spacing: 1px;">Itemized List</h5>
                
                @foreach($submission->items as $index => $item)
                    {{-- Hidden input to store individual item status --}}
                    <input type="hidden" name="items[{{ $item->submission_item_id }}][status]" id="input-status-{{ $item->submission_item_id }}" value="pending">

                    <div class="card border-0 shadow-lg rounded-4 mb-4 overflow-hidden bg-white item-row" id="item-row-{{ $item->submission_item_id }}">
                        <div class="bg-dark text-white px-4 py-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">ITEM #{{ $index + 1 }}: {{ strtoupper($item->item_name) }}</h6>
                            <span class="badge bg-success px-3">QTY: {{ $item->quantity }}</span>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-md-8 border-end">
                                    <div class="row g-3 mb-4">
                                        <div class="col-md-4">
                                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-1">Serial Number</label>
                                            <span class="fw-bold font-monospace text-dark small">{{ $item->serial_number ?? 'N/A' }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-1">Unit Cost</label>
                                            <span class="fw-bold text-dark">₦{{ number_format($item->unit_cost ?? $item->cost ?? 0, 2) }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-1">Item Condition</label>
                                            <span class="badge bg-info-subtle text-info border border-info px-3">{{ strtoupper($item->condition ?? 'Good') }}</span>
                                        </div>
                                        <div class="col-12">
                                            <label class="text-muted fw-bold text-uppercase x-small d-block mb-1">Asset Location</label>
                                            <div class="small fw-bold p-2 bg-light rounded border-start border-3 border-dark text-primary">
                                                @php
                                                    $staff = $submission->submittedBy;
                                                    $parent = $staff->faculty->faculty_name ?? $staff->office->office_name ?? $staff->institute->institute_name ?? 'College General';
                                                    $child = $staff->department->dept_name ?? $staff->unit->unit_name ?? 'Main Division';
                                                @endphp
                                                {{ $parent }} <i class="fas fa-chevron-right mx-1 small text-muted"></i> {{ $child }}
                                            </div>
                                        </div>
                                    </div>
                                    <label class="text-muted fw-bold text-uppercase x-small d-block mb-2">Internal Notes</label>
                                    <p class="small bg-light p-3 rounded border mb-0">{{ $item->item_notes ?? 'No item notes.' }}</p>
                                </div>

                                <div class="col-md-4 text-center d-flex flex-column justify-content-center">
                                    <label class="text-muted fw-bold text-uppercase x-small mb-1">Subtotal</label>
                                    <h4 class="fw-bolder text-dark mb-4">₦{{ number_format(($item->unit_cost ?? $item->cost ?? 0) * $item->quantity, 2) }}</h4>

                                    <div class="btn-group w-100 shadow-sm border rounded-pill overflow-hidden bg-white">
                                        <button type="button" onclick="markItem({{ $item->submission_item_id }}, 'rejected')" class="btn btn-white text-danger fw-bold border-end" id="rej-{{ $item->submission_item_id }}">Reject</button>
                                        <button type="button" onclick="markItem({{ $item->submission_item_id }}, 'approved')" class="btn btn-white text-success fw-bold" id="app-{{ $item->submission_item_id }}">Approve</button>
                                    </div>
                                    <small id="status-label-{{ $item->submission_item_id }}" class="mt-3 text-muted x-small italic fw-bold">Pending Audit</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- SIDEBAR --}}
            <div class="col-lg-3">
                <div class="sticky-top" style="top: 2rem;">
                    {{-- ... (Submitted By Card Same as before) ... --}}
                    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white mb-4">
                        <div class="bg-dark p-3 text-center">
                            <h6 class="text-white-50 text-uppercase fw-bold mb-0 x-small tracking-widest">Submitted By</h6>
                        </div>
                        <div class="p-4 text-center">
                            <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 70px; height: 70px;">
                                <i class="fas fa-user-shield fa-2x text-dark"></i>
                            </div>
                            <h6 class="fw-bolder text-dark mb-1">{{ $submission->submittedBy->username }}</h6>
                            <span class="x-small text-uppercase fw-bold text-muted d-block mb-3">Inventory Officer</span>
                            <div class="text-start border-top pt-3 mt-3">
                                <label class="x-small text-muted fw-bold text-uppercase d-block mb-1">Originating Source</label>
                                <p class="small fw-bold text-dark mb-1">{{ $submission->submittedBy->faculty->faculty_name ?? $submission->submittedBy->office->office_name ?? 'College' }}</p>
                                @if($submission->submittedBy->department || $submission->submittedBy->unit)
                                    <p class="x-small text-muted mb-0"><i class="fas fa-level-up-alt fa-rotate-90 me-1"></i> {{ $submission->submittedBy->department->dept_name ?? $submission->submittedBy->unit->unit_name }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="card border-0 shadow rounded-4 bg-dark p-4">
                        <p class="x-small text-white-50 fw-bold mb-0 italic">
                            <strong>Audit Review Notice: Approving this batch will permanently register these items into the Medicine College inventory ledger.</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL (Now inside the form) --}}
    <div class="modal fade" id="auditDecisionModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body p-5 text-center">
                    <div id="modal_icon_container" class="mb-4"></div>
                    <h3 class="fw-bolder text-dark" id="modal_title">Decision</h3>
                    <div class="text-start mt-4">
                        <label class="x-small fw-bold text-uppercase text-muted mb-2">Audit Remarks & Feedback</label>
                        <textarea name="comments" class="form-control border shadow-sm bg-light" rows="3" placeholder="Enter comments for the records..." required></textarea>
                    </div>
                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-dark btn-lg rounded-pill fw-bold" id="modal_submit_btn">Confirm Action</button>
                        <button type="button" class="btn btn-link text-muted fw-bold text-decoration-none" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
    /* ... (Styles same as previous) ... */
    .pulse-container { position: relative; width: 12px; height: 12px; display: flex; align-items: center; justify-content: center; }
    .pulse-dot-yellow { width: 10px; height: 10px; background: #ffc107; border-radius: 50%; z-index: 2; }
    .pulse-ring-yellow { position: absolute; width: 25px; height: 25px; border: 3px solid #ffc107; border-radius: 50%; animation: pulse-ring 1.5s infinite; }
    .pulse-active { animation: pulse-dot 1.2s infinite ease-in-out; }
    @keyframes pulse-ring { 0% { transform: scale(0.33); opacity: 1; } 80%, 100% { transform: scale(1.2); opacity: 0; } }
    @keyframes pulse-dot { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }
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

        // Update Hidden Input Value
        input.value = status;

        row.classList.remove('item-verified', 'item-flagged');
        if(status === 'approved') {
            row.classList.add('item-verified');
            app.className = "btn bg-success text-white fw-bold";
            rej.className = "btn btn-white text-danger fw-bold border-end";
            label.innerText = "VERIFIED";
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
            title.innerText = "Finalize Batch Audit";
            icon.innerHTML = '<i class="fas fa-check-double fa-4x text-success"></i>';
            btn.className = "btn btn-success btn-lg rounded-pill fw-bold";
        } else {
            title.innerText = "Reject Entire Batch";
            icon.innerHTML = '<i class="fas fa-exclamation-triangle fa-4x text-danger"></i>';
            btn.className = "btn btn-danger btn-lg rounded-pill fw-bold";
        }
    }
</script>
@endsection