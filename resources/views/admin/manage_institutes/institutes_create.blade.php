@extends('layouts.admin')

@section('title', 'Add New Institute | College of Medicine')

@section('content')
<div class="min-vh-100 py-4 px-3 px-lg-5 bg-slate-50">
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.institutes.index') }}" 
               class="btn btn-white border border-slate-200 rounded-circle shadow-sm d-flex align-items-center justify-content-center" 
               style="width:44px; height:44px;">
                <i class="fas fa-arrow-left text-slate-400"></i>
            </a>
            <div>
                <h1 class="fw-black text-slate-900 mb-1" style="font-size:1.75rem; letter-spacing:-0.02em;">Add New Institute</h1>
                <p class="text-slate-600 mb-0" style="font-size:0.88rem;">Establish a new research or academic body within the College.</p>
            </div>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.institutes.index') }}" class="btn btn-white border-slate-200 fw-bold px-4">Cancel</a>
            <button type="submit" form="instituteForm" class="btn text-white fw-black d-flex align-items-center gap-2 rounded-3 shadow-lg"
               style="background:linear-gradient(135deg, #f59e0b, #d97706); font-size:0.82rem; letter-spacing:0.05em; text-transform:uppercase; padding:0.75rem 1.8rem; border:none; white-space: nowrap;">
                <i class="fas fa-save"></i> Save Institute
            </button>
        </div>
    </div>


    
    <div class="row g-4">
        {{-- Main Form --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-slate-800">
                        <i class="fas fa-flask me-2 text-orange-600"></i> Registration Details
                    </h5>
                </div> 
                
                <div class="card-body p-4">
                    <form action="{{ route('admin.institutes.store') }}" method="POST" id="instituteForm">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label fw-bold text-slate-700 small uppercase tracking-wider">Institute Name <span class="text-danger">*</span></label>
                            <input type="text" name="institute_name" class="form-control form-control-lg border-slate-200 bg-slate-50" 
                                   placeholder="e.g. Institute of Advanced Medical Research" required>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-slate-700 small uppercase tracking-wider">Institute Code</label>
                                <input type="text" name="institute_code" class="form-control border-slate-200 bg-slate-50 text-uppercase" 
                                       placeholder="e.g. IAMR">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-slate-700 small uppercase tracking-wider">Initial Status</label>
                                <select name="is_active" class="form-select border-slate-200 bg-slate-50">
                                    <option value="active">Active </option>
                                    <option value="inactive">Inactive </option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-slate-700 small uppercase tracking-wider">Assign Director</label>
                            <select id="institute_director_id" name="institute_director_id" class="form-select select2-remote border-slate-200 bg-slate-50">
                                <option value="">Select Director</option>
                            </select>
                            <div class="form-text text-slate-400">Search by name or staff ID. This person will be the primary custodian.</div>
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold text-slate-700 small uppercase tracking-wider">Physical Location/Address</label>
                            <textarea id="institute_address" name="institute_address" class="form-control border-slate-200 bg-slate-50" rows="3" 
                                      placeholder="Building, Floor, or Room Number..."></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar Context --}}
        <div class="col-lg-4">
            {{-- Administrative Info Card --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-bold text-slate-800">
                        <i class="fas fa-info-circle me-2 text-orange-600"></i> Administrative Info
                    </h5>
                </div>
                <div class="card-body p-4">
                    {{-- Hierarchy Visual --}}
                    <div class="d-flex align-items-center mb-4">
                        <div class="position-relative d-flex flex-column align-items-center">
                            <div class="rounded-circle bg-slate-100 d-flex align-items-center justify-content-center border" style="width: 40px; height: 40px;">
                                <i class="fas fa-university text-slate-400 small"></i>
                            </div>
                            <div class="bg-slate-200" style="width: 2px; height: 20px;"></div>
                            <div class="rounded-circle bg-orange-50 d-flex align-items-center justify-content-center border border-orange-200" style="width: 40px; height: 40px;">
                                <i class="fas fa-flask text-orange-600 small"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <div class="mb-3">
                                <small class="text-slate-400 d-block uppercase tracking-tighter fw-bold" style="font-size: 0.65rem;">Organization</small>
                                <span class="fw-bold text-slate-700">College of Medicine</span>
                            </div>
                            <div>
                                <small class="text-slate-400 d-block uppercase tracking-tighter fw-bold" style="font-size: 0.65rem;">New Entity</small>
                                <span class="fw-bold text-orange-600">Research Institute</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-3 border border-slate-100">
                        <h6 class="fw-bold text-slate-800 mb-2" style="font-size: 0.85rem;">Why Create an Institute?</h6>
                        <p class="small text-slate-600 mb-0">
                            Institutes are independent research hubs. Unlike <strong>Units</strong>, they often manage their own external funding sources and specialized laboratory equipment.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Audit/Inventory Requirement Note --}}
            <div class="bg-indigo-900 rounded-4 p-4 text-white shadow-lg position-relative overflow-hidden">
                <i class="fas fa-shield-alt position-absolute end-0 bottom-0 mb-n3 me-n2 opacity-10" style="font-size: 8rem;"></i>
                <div class="position-relative">
                    <h6 class="fw-black uppercase mb-2" style="letter-spacing: 0.1em; font-size: 0.75rem;">Inventory Protocol</h6>
                    <p class="small mb-0 opacity-75">
                        Once created, all assets purchased via the Institute's code will require bi-annual verification. Ensure the <strong>Director</strong> assigned has a valid system account.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900 !important; }
    .uppercase { text-transform: uppercase; }
    .btn-white { background: #fff; }
    .bg-slate-50 { background-color: #f8fafc !important; }
    .text-orange-600 { color: #ea580c !important; }
</style>
@endsection

@push('scripts')
    @include('admin.partials.entity_scripts', [
        'formId' => 'instituteForm', 
        'selectId' => 'institute_director_id', // Add this if your partial supports it
        'searchRoute' => route('admin.search.staff') 
    ])
@endpush