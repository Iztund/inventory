@extends('layouts.auditor')

@section('title', 'Audit Reports')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="mb-5 text-center">
        <h2 class="fw-bold">Inventory Reports Hub 📑</h2>
        <p class="text-muted mx-auto" style="max-width: 500px;">Generate certified inventory reports for the College of Medicine administration and finance departments.</p>
    </div>

    <div class="row g-4 justify-content-center">
        {{-- Report Card: Master List --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <div class="bg-soft-primary text-primary rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <i class="fas fa-list-ul fa-2x"></i>
                </div>
                <h5 class="fw-bold">Master Inventory</h5>
                <p class="small text-muted mb-4">Complete list of all verified assets across the entire college.</p>
                <button class="btn btn-primary w-100 rounded-pill">Download Excel</button>
            </div>
        </div>

        {{-- Report Card: Entity Specific --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <div class="bg-soft-success text-success rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <i class="fas fa-university fa-2x"></i>
                </div>
                <h5 class="fw-bold">Entity Report</h5>
                <p class="small text-muted mb-4">Detailed breakdown of assets filtered by Faculty, Office, or Unit.</p>
                <button class="btn btn-success w-100 rounded-pill text-white">Generate PDF</button>
            </div>
        </div>

        {{-- Report Card: Financial Audit --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4 border-bottom border-4 border-warning">
                <div class="bg-soft-warning text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <i class="fas fa-file-invoice-dollar fa-2x"></i>
                </div>
                <h5 class="fw-bold">Financial Summary</h5>
                <p class="small text-muted mb-4">Report focused on asset values, depreciation, and funding sources.</p>
                <button class="btn btn-warning w-100 rounded-pill text-dark fw-bold">Download Report</button>
            </div>
        </div>
    </div>
</div>
@endsection