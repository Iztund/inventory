@extends('layouts.staff')

@section('title', 'Staff Guidelines & Manual')

@section('content')

<div class="container-fluid px-3 px-lg-5 py-4" style="max-width: 1600px;">
    
    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
        <div>
            <h1 class="fw-black text-slate-900 mb-2" style="font-size:1.8rem; letter-spacing:-0.02em;">
                Staff Guidelines & Manual
            </h1>
            <p class="text-slate-600 mb-0" style="font-size:0.9rem;">
                Official procedures for inventory management and asset tracking
            </p>
        </div>
        <button onclick="window.print()" 
                class="btn btn-white border border-slate-200 rounded-3 px-4 py-2 fw-bold shadow-sm d-flex align-items-center gap-2"
                style="font-size:0.82rem;">
            <i class="fas fa-download text-slate-600"></i>
            <span>Download PDF</span>
        </button>
    </div>

    <div class="row g-4">
        
        {{-- Left: Table of Contents --}}
        <div class="col-lg-3">
            <div class="sticky-top" style="top:85px;">
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden mb-4 d-none d-lg-block">
                    <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                         style="background:linear-gradient(to right, #f8fafc, #f1f5f9);">
                        <h6 class="fw-black text-slate-900 mb-0 d-flex align-items-center gap-2" style="font-size:0.85rem;">
                            <i class="fas fa-list text-emerald-600"></i>
                            Table of Contents
                        </h6>
                    </div>
                    <div class="list-group list-group-flush" id="manual-nav">
                        <a href="#introduction" 
                           class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-center gap-2"
                           style="font-size:0.82rem; transition:all 0.2s;">
                            <i class="fas fa-info-circle text-slate-400" style="font-size:0.85rem;"></i>
                            Introduction
                        </a>
                        <a href="#getting-started" 
                           class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-center gap-2"
                           style="font-size:0.82rem; transition:all 0.2s;">
                            <i class="fas fa-rocket text-slate-400" style="font-size:0.85rem;"></i>
                            Getting Started
                        </a>
                        <a href="#submissions" 
                           class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-center gap-2"
                           style="font-size:0.82rem; transition:all 0.2s;">
                            <i class="fas fa-plus-square text-slate-400" style="font-size:0.85rem;"></i>
                            New Submissions
                        </a>
                        <a href="#tagging" 
                           class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-center gap-2"
                           style="font-size:0.82rem; transition:all 0.2s;">
                            <i class="fas fa-tag text-slate-400" style="font-size:0.85rem;"></i>
                            Asset Tagging
                        </a>
                        <a href="#tracking" 
                           class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-center gap-2"
                           style="font-size:0.82rem; transition:all 0.2s;">
                            <i class="fas fa-search text-slate-400" style="font-size:0.85rem;"></i>
                            Tracking & Updates
                        </a>
                        <a href="#audit" 
                           class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-center gap-2"
                           style="font-size:0.82rem; transition:all 0.2s;">
                            <i class="fas fa-check-double text-slate-400" style="font-size:0.85rem;"></i>
                            Annual Audits
                        </a>
                        <a href="#maintenance" 
                           class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-center gap-2"
                           style="font-size:0.82rem; transition:all 0.2s;">
                            <i class="fas fa-tools text-slate-400" style="font-size:0.85rem;"></i>
                            Maintenance
                        </a>
                        <a href="#best-practices" 
                           class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-center gap-2"
                           style="font-size:0.82rem; transition:all 0.2s;">
                            <i class="fas fa-star text-slate-400" style="font-size:0.85rem;"></i>
                            Best Practices
                        </a>
                        <a href="#faq" 
                           class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-center gap-2"
                           style="font-size:0.82rem; transition:all 0.2s;">
                            <i class="fas fa-question-circle text-slate-400" style="font-size:0.85rem;"></i>
                            FAQ
                        </a>
                        <a href="#support" 
                           class="list-group-item list-group-item-action border-0 py-3 d-flex align-items-center gap-2"
                           style="font-size:0.82rem; transition:all 0.2s;">
                            <i class="fas fa-headset text-slate-400" style="font-size:0.85rem;"></i>
                            Support
                        </a>
                    </div>
                </div>

                {{-- Mobile Navigation --}}
                <div class="d-lg-none mb-4">
                    <div class="d-flex gap-2 overflow-auto pb-2" style="-webkit-overflow-scrolling:touch;">
                        <a href="#introduction" class="btn btn-sm btn-white border border-slate-200 rounded-pill px-3 fw-bold text-nowrap">Introduction</a>
                        <a href="#submissions" class="btn btn-sm btn-white border border-slate-200 rounded-pill px-3 fw-bold text-nowrap">Submissions</a>
                        <a href="#tagging" class="btn btn-sm btn-white border border-slate-200 rounded-pill px-3 fw-bold text-nowrap">Tagging</a>
                        <a href="#audit" class="btn btn-sm btn-white border border-slate-200 rounded-pill px-3 fw-bold text-nowrap">Audits</a>
                        <a href="#faq" class="btn btn-sm btn-white border border-slate-200 rounded-pill px-3 fw-bold text-nowrap">FAQ</a>
                        <a href="#support" class="btn btn-sm btn-white border border-slate-200 rounded-pill px-3 fw-bold text-nowrap">Support</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Content --}}
        <div class="col-lg-9">
            
            {{-- Introduction --}}
            <section id="introduction" class="mb-5 scroll-mt-5">
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                         style="background:linear-gradient(to right, #f0fdf4, #dcfce7);">
                        <h4 class="fw-black text-slate-900 mb-0 d-flex align-items-center gap-2" style="font-size:1.2rem;">
                            <div class="rounded-circle bg-emerald-600 d-flex align-items-center justify-content-center text-white"
                                 style="width:32px; height:32px; font-size:0.9rem;">
                                1
                            </div>
                            Introduction
                        </h4>
                    </div>
                    <div class="p-4">
                        <p class="text-slate-700 mb-4" style="font-size:0.92rem; line-height:1.7;">
                            The College of Medicine Inventory System (COMIS) is a comprehensive digital platform designed to track and manage all physical assets across the institution, including medical equipment, furniture, laboratory instruments, and administrative resources.
                        </p>
                        
                        <div class="rounded-3 p-4 mb-4" style="background:linear-gradient(135deg, #f0fdf4, #dcfce7); border-left:4px solid #059669;">
                            <div class="d-flex align-items-start gap-3">
                                <i class="fas fa-bullseye text-emerald-600" style="font-size:1.8rem;"></i>
                                <div>
                                    <h6 class="fw-bold text-emerald-900 mb-2">System Objectives</h6>
                                    <ul class="text-slate-700 mb-0" style="font-size:0.88rem;">
                                        <li>Maintain accurate digital records of all institutional assets</li>
                                        <li>Ensure accountability and prevent asset loss</li>
                                        <li>Facilitate annual audit processes</li>
                                        <li>Track asset lifecycle from acquisition to disposal</li>
                                        <li>Generate reports for administrative and regulatory purposes</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-slate-900 mb-3" style="font-size:0.95rem;">Coverage Scope</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 border border-slate-200 bg-slate-50">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-graduation-cap text-emerald-600"></i>
                                        <h6 class="fw-bold text-slate-900 mb-0" style="font-size:0.85rem;">Academic Units</h6>
                                    </div>
                                    <p class="text-slate-600 mb-0" style="font-size:0.78rem;">Faculties, Departments, Research Institutes</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded-3 border border-slate-200 bg-slate-50">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="fas fa-briefcase text-emerald-600"></i>
                                        <h6 class="fw-bold text-slate-900 mb-0" style="font-size:0.85rem;">Administrative Units</h6>
                                    </div>
                                    <p class="text-slate-600 mb-0" style="font-size:0.78rem;">Offices, Administrative Departments</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Getting Started --}}
            <section id="getting-started" class="mb-5 scroll-mt-5">
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                         style="background:linear-gradient(to right, #f0fdf4, #dcfce7);">
                        <h4 class="fw-black text-slate-900 mb-0 d-flex align-items-center gap-2" style="font-size:1.2rem;">
                            <div class="rounded-circle bg-emerald-600 d-flex align-items-center justify-content-center text-white"
                                 style="width:32px; height:32px; font-size:0.9rem;">
                                2
                            </div>
                            Getting Started
                        </h4>
                    </div>
                    <div class="p-4">
                        <h6 class="fw-bold text-slate-900 mb-3" style="font-size:0.95rem;">Access Requirements</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="text-center p-3 rounded-3 bg-emerald-50 border border-emerald-100">
                                    <i class="fas fa-user-shield text-emerald-600 mb-2" style="font-size:1.5rem;"></i>
                                    <h6 class="fw-bold text-slate-900 mb-1" style="font-size:0.82rem;">Staff Account</h6>
                                    <p class="text-slate-600 mb-0" style="font-size:0.72rem;">Assigned by IT Unit</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 rounded-3 bg-emerald-50 border border-emerald-100">
                                    <i class="fas fa-id-badge text-emerald-600 mb-2" style="font-size:1.5rem;"></i>
                                    <h6 class="fw-bold text-slate-900 mb-1" style="font-size:0.82rem;">Unit Assignment</h6>
                                    <p class="text-slate-600 mb-0" style="font-size:0.72rem;">Linked to your department</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 rounded-3 bg-emerald-50 border border-emerald-100">
                                    <i class="fas fa-key text-emerald-600 mb-2" style="font-size:1.5rem;"></i>
                                    <h6 class="fw-bold text-slate-900 mb-1" style="font-size:0.82rem;">Permissions</h6>
                                    <p class="text-slate-600 mb-0" style="font-size:0.72rem;">Submit & view assets</p>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-slate-900 mb-3" style="font-size:0.95rem;">Dashboard Overview</h6>
                        <p class="text-slate-700 mb-3" style="font-size:0.88rem;">
                            After logging in, you'll see your personalized dashboard with:
                        </p>
                        <ul class="text-slate-700" style="font-size:0.88rem; line-height:1.8;">
                            <li><strong>Submission Statistics:</strong> Total, pending, and approved items</li>
                            <li><strong>Recent Activity:</strong> Your latest submissions and their status</li>
                            <li><strong>Quick Actions:</strong> Create new submission, view assets, access guidelines</li>
                        </ul>
                    </div>
                </div>
            </section>

            {{-- New Submissions --}}
            <section id="submissions" class="mb-5 scroll-mt-5">
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                         style="background:linear-gradient(to right, #f0fdf4, #dcfce7);">
                        <h4 class="fw-black text-slate-900 mb-0 d-flex align-items-center gap-2" style="font-size:1.2rem;">
                            <div class="rounded-circle bg-emerald-600 d-flex align-items-center justify-content-center text-white"
                                 style="width:32px; height:32px; font-size:0.9rem;">
                                3
                            </div>
                            Creating New Submissions
                        </h4>
                    </div>
                    <div class="p-4">
                        <p class="text-slate-700 mb-4" style="font-size:0.92rem;">
                            Follow these steps to submit assets for audit verification:
                        </p>

                        {{-- Step 1 --}}
                        <div class="d-flex align-items-start gap-3 mb-4 pb-4 border-bottom border-slate-100">
                            <div class="rounded-circle bg-emerald-600 text-white d-flex align-items-center justify-content-center fw-black flex-shrink-0"
                                 style="width:40px; height:40px; font-size:1rem;">
                                1
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-slate-900 mb-2" style="font-size:0.92rem;">Navigate to Submissions</h6>
                                <p class="text-slate-700 mb-2" style="font-size:0.85rem;">
                                    Click <strong>"New Submission"</strong> in the sidebar menu or use the quick action button on your dashboard.
                                </p>
                                <div class="rounded-2 bg-slate-50 border border-slate-200 px-3 py-2" style="font-size:0.78rem;">
                                    <i class="fas fa-lightbulb text-amber-600 me-1"></i>
                                    <strong>Tip:</strong> Prepare all asset information before starting
                                </div>
                            </div>
                        </div>

                        {{-- Step 2 --}}
                        <div class="d-flex align-items-start gap-3 mb-4 pb-4 border-bottom border-slate-100">
                            <div class="rounded-circle bg-emerald-600 text-white d-flex align-items-center justify-content-center fw-black flex-shrink-0"
                                 style="width:40px; height:40px; font-size:1rem;">
                                2
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-slate-900 mb-2" style="font-size:0.92rem;">Select Entry Type</h6>
                                <p class="text-slate-700 mb-3" style="font-size:0.85rem;">Choose the appropriate submission type:</p>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="p-2 rounded-2 bg-emerald-50 border border-emerald-100" style="font-size:0.78rem;">
                                            <strong class="text-emerald-700">New Purchase:</strong> <span class="text-slate-600">Newly acquired items</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-2 rounded-2 bg-blue-50 border border-blue-100" style="font-size:0.78rem;">
                                            <strong class="text-blue-700">Transfer:</strong> <span class="text-slate-600">Items moved between units</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-2 rounded-2 bg-amber-50 border border-amber-100" style="font-size:0.78rem;">
                                            <strong class="text-amber-700">Disposal:</strong> <span class="text-slate-600">Items being retired</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-2 rounded-2 bg-slate-50 border border-slate-200" style="font-size:0.78rem;">
                                            <strong class="text-slate-700">Audit/Update:</strong> <span class="text-slate-600">Existing item updates</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Step 3 --}}
                        <div class="d-flex align-items-start gap-3 mb-4 pb-4 border-bottom border-slate-100">
                            <div class="rounded-circle bg-emerald-600 text-white d-flex align-items-center justify-content-center fw-black flex-shrink-0"
                                 style="width:40px; height:40px; font-size:1rem;">
                                3
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-slate-900 mb-2" style="font-size:0.92rem;">Fill Asset Details</h6>
                                <p class="text-slate-700 mb-2" style="font-size:0.85rem;">Provide complete information:</p>
                                <ul class="text-slate-700 mb-0" style="font-size:0.82rem; line-height:1.7;">
                                    <li><strong>Category & Sub-category:</strong> Select from dropdown menus</li>
                                    <li><strong>Item Name:</strong> Be specific (e.g., "Dell Latitude 5420 Laptop")</li>
                                    <li><strong>Quantity:</strong> Number of identical items</li>
                                    <li><strong>Unit Cost:</strong> Price per item (for new purchases)</li>
                                    <li><strong>Serial Number:</strong> If available (recommended)</li>
                                    <li><strong>Notes:</strong> Condition, specifications, or special details</li>
                                </ul>
                            </div>
                        </div>

                        {{-- Step 4 --}}
                        <div class="d-flex align-items-start gap-3 mb-4 pb-4 border-bottom border-slate-100">
                            <div class="rounded-circle bg-emerald-600 text-white d-flex align-items-center justify-content-center fw-black flex-shrink-0"
                                 style="width:40px; height:40px; font-size:1rem;">
                                4
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-slate-900 mb-2" style="font-size:0.92rem;">Upload Supporting Documents</h6>
                                <p class="text-slate-700 mb-3" style="font-size:0.85rem;">
                                    Attach clear photos or scanned documents:
                                </p>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-2 p-2 rounded-2 bg-slate-50">
                                            <i class="fas fa-check-circle text-emerald-600"></i>
                                            <span style="font-size:0.78rem;">Photos of the asset</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-2 p-2 rounded-2 bg-slate-50">
                                            <i class="fas fa-check-circle text-emerald-600"></i>
                                            <span style="font-size:0.78rem;">Purchase receipts/invoices</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-2 p-2 rounded-2 bg-slate-50">
                                            <i class="fas fa-check-circle text-emerald-600"></i>
                                            <span style="font-size:0.78rem;">Transfer documents</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center gap-2 p-2 rounded-2 bg-slate-50">
                                            <i class="fas fa-check-circle text-emerald-600"></i>
                                            <span style="font-size:0.78rem;">Warranty certificates</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="rounded-2 bg-amber-50 border border-amber-200 px-3 py-2" style="font-size:0.75rem;">
                                    <i class="fas fa-exclamation-triangle text-amber-600 me-1"></i>
                                    <strong>Note:</strong> Maximum file size is 10MB. Supported formats: JPG, PNG, PDF
                                </div>
                            </div>
                        </div>

                        {{-- Step 5 --}}
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-emerald-600 text-white d-flex align-items-center justify-content-center fw-black flex-shrink-0"
                                 style="width:40px; height:40px; font-size:1rem;">
                                5
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="fw-bold text-slate-900 mb-2" style="font-size:0.92rem;">Review & Submit</h6>
                                <p class="text-slate-700 mb-3" style="font-size:0.85rem;">
                                    Before submitting, verify all information is accurate. You can add multiple items in a single submission.
                                </p>
                                <div class="rounded-2 bg-emerald-50 border border-emerald-200 px-3 py-2" style="font-size:0.78rem;">
                                    <i class="fas fa-info-circle text-emerald-600 me-1"></i>
                                    After submission, your items will be sent to the audit team for review. You'll receive a notification once processed.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Asset Tagging --}}
            <section id="tagging" class="mb-5 scroll-mt-5">
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                         style="background:linear-gradient(to right, #f0fdf4, #dcfce7);">
                        <h4 class="fw-black text-slate-900 mb-0 d-flex align-items-center gap-2" style="font-size:1.2rem;">
                            <div class="rounded-circle bg-emerald-600 d-flex align-items-center justify-content-center text-white"
                                 style="width:32px; height:32px; font-size:0.9rem;">
                                4
                            </div>
                            Asset Tagging System
                        </h4>
                    </div>
                    <div class="p-4">
                        <p class="text-slate-700 mb-4" style="font-size:0.92rem;">
                            All approved assets must be physically tagged with a unique identifier for tracking and verification purposes.
                        </p>

                        <h6 class="fw-bold text-slate-900 mb-3" style="font-size:0.95rem;">Tag Format</h6>
                        <div class="rounded-3 p-4 mb-4 bg-slate-50 border border-slate-200">
                            <div class="text-center">
                                <code class="px-4 py-3 rounded-3 bg-white border border-slate-300 fw-black d-inline-block" 
                                      style="font-size:1.2rem; letter-spacing:0.1em; color:#059669;">
                                    DEPT/25/0001
                                </code>
                                <div class="row g-2 mt-3">
                                    <div class="col-md-4">
                                        <div class="p-2 rounded-2 bg-white border border-slate-200">
                                            <div class="fw-bold text-emerald-600" style="font-size:0.75rem;">DEPT</div>
                                            <div class="text-slate-600" style="font-size:0.72rem;">Unit Code</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-2 rounded-2 bg-white border border-slate-200">
                                            <div class="fw-bold text-emerald-600" style="font-size:0.75rem;">25</div>
                                            <div class="text-slate-600" style="font-size:0.72rem;">Year (2025)</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="p-2 rounded-2 bg-white border border-slate-200">
                                            <div class="fw-bold text-emerald-600" style="font-size:0.75rem;">0001</div>
                                            <div class="text-slate-600" style="font-size:0.72rem;">Sequence Number</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-slate-900 mb-3" style="font-size:0.95rem;">Tag Application Process</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="rounded-3 p-3 border border-slate-200 bg-slate-50 h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="rounded-circle bg-emerald-600 text-white d-flex align-items-center justify-content-center fw-bold"
                                             style="width:24px; height:24px; font-size:0.75rem;">
                                            1
                                        </div>
                                        <h6 class="fw-bold text-slate-900 mb-0" style="font-size:0.85rem;">Approval</h6>
                                    </div>
                                    <p class="text-slate-600 mb-0" style="font-size:0.78rem;">
                                        Wait for audit team to approve your submission
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="rounded-3 p-3 border border-slate-200 bg-slate-50 h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="rounded-circle bg-emerald-600 text-white d-flex align-items-center justify-content-center fw-bold"
                                             style="width:24px; height:24px; font-size:0.75rem;">
                                            2
                                        </div>
                                        <h6 class="fw-bold text-slate-900 mb-0" style="font-size:0.85rem;">Tag Assignment</h6>
                                    </div>
                                    <p class="text-slate-600 mb-0" style="font-size:0.78rem;">
                                        System generates unique tag for approved item
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="rounded-3 p-3 border border-slate-200 bg-slate-50 h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="rounded-circle bg-emerald-600 text-white d-flex align-items-center justify-content-center fw-bold"
                                             style="width:24px; height:24px; font-size:0.75rem;">
                                            3
                                        </div>
                                        <h6 class="fw-bold text-slate-900 mb-0" style="font-size:0.85rem;">Physical Tag</h6>
                                    </div>
                                    <p class="text-slate-600 mb-0" style="font-size:0.78rem;">
                                        Collect printed tag from unit head
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="rounded-3 p-3 border border-slate-200 bg-slate-50 h-100">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="rounded-circle bg-emerald-600 text-white d-flex align-items-center justify-content-center fw-bold"
                                             style="width:24px; height:24px; font-size:0.75rem;">
                                            4
                                        </div>
                                        <h6 class="fw-bold text-slate-900 mb-0" style="font-size:0.85rem;">Attachment</h6>
                                    </div>
                                    <p class="text-slate-600 mb-0" style="font-size:0.78rem;">
                                        Affix tag to visible location on asset
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3 p-3 mt-4" style="background:#fffbeb; border-left:4px solid #f59e0b;">
                            <div class="fw-bold text-amber-800 mb-1" style="font-size:0.85rem;">
                                <i class="fas fa-exclamation-circle me-1"></i> Important
                            </div>
                            <p class="text-slate-700 mb-0" style="font-size:0.8rem;">
                                Do not remove or damage asset tags. Tags must remain visible for annual audits. Report damaged tags immediately for replacement.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Tracking & Updates --}}
            <section id="tracking" class="mb-5 scroll-mt-5">
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                         style="background:linear-gradient(to right, #f0fdf4, #dcfce7);">
                        <h4 class="fw-black text-slate-900 mb-0 d-flex align-items-center gap-2" style="font-size:1.2rem;">
                            <div class="rounded-circle bg-emerald-600 d-flex align-items-center justify-content-center text-white"
                                 style="width:32px; height:32px; font-size:0.9rem;">
                                5
                            </div>
                            Tracking & Status Updates
                        </h4>
                    </div>
                    <div class="p-4">
                        <h6 class="fw-bold text-slate-900 mb-3" style="font-size:0.95rem;">Submission Status Workflow</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="text-center p-3 rounded-3 bg-amber-50 border border-amber-200">
                                    <i class="fas fa-clock text-amber-600 mb-2" style="font-size:1.5rem;"></i>
                                    <h6 class="fw-bold text-amber-800 mb-1" style="font-size:0.85rem;">Pending Review</h6>
                                    <p class="text-slate-600 mb-0" style="font-size:0.72rem;">Awaiting audit verification</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 rounded-3 bg-emerald-50 border border-emerald-200">
                                    <i class="fas fa-check-circle text-emerald-600 mb-2" style="font-size:1.5rem;"></i>
                                    <h6 class="fw-bold text-emerald-800 mb-1" style="font-size:0.85rem;">Approved</h6>
                                    <p class="text-slate-600 mb-0" style="font-size:0.72rem;">Successfully verified</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 rounded-3 bg-rose-50 border border-rose-200">
                                    <i class="fas fa-times-circle text-rose-600 mb-2" style="font-size:1.5rem;"></i>
                                    <h6 class="fw-bold text-rose-800 mb-1" style="font-size:0.85rem;">Rejected</h6>
                                    <p class="text-slate-600 mb-0" style="font-size:0.72rem;">Needs correction</p>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-slate-900 mb-3" style="font-size:0.95rem;">Monitoring Your Submissions</h6>
                        <ul class="text-slate-700" style="font-size:0.88rem; line-height:1.8;">
                            <li>Check <strong>"My Submissions"</strong> regularly for status updates</li>
                            <li>View detailed feedback from auditors on rejected items</li>
                            <li>Edit and resubmit rejected items with corrections</li>
                            <li>Download submission receipts for your records</li>
                        </ul>
                    </div>
                </div>
            </section>

            {{-- Annual Audits --}}
            <section id="audit" class="mb-5 scroll-mt-5">
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                         style="background:linear-gradient(to right, #f0fdf4, #dcfce7);">
                        <h4 class="fw-black text-slate-900 mb-0 d-flex align-items-center gap-2" style="font-size:1.2rem;">
                            <div class="rounded-circle bg-emerald-600 d-flex align-items-center justify-content-center text-white"
                                 style="width:32px; height:32px; font-size:0.9rem;">
                                6
                            </div>
                            Annual Physical Audits
                        </h4>
                    </div>
                    <div class="p-4">
                        <p class="text-slate-700 mb-4" style="font-size:0.92rem;">
                            Physical verification exercises are conducted twice annually to ensure accuracy of digital records.
                        </p>

                        <div class="rounded-3 p-4 mb-4" style="background:linear-gradient(135deg, #dbeafe, #bfdbfe); border-left:4px solid #3b82f6;">
                            <h6 class="fw-bold text-blue-900 mb-2">Audit Schedule</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <div class="p-2 rounded-2 bg-white bg-opacity-75">
                                        <div class="fw-bold text-blue-700" style="font-size:0.82rem;">First Quarter Audit</div>
                                        <div class="text-slate-600" style="font-size:0.75rem;">January - March</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-2 rounded-2 bg-white bg-opacity-75">
                                        <div class="fw-bold text-blue-700" style="font-size:0.82rem;">Third Quarter Audit</div>
                                        <div class="text-slate-600" style="font-size:0.75rem;">July - September</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-slate-900 mb-3" style="font-size:0.95rem;">Your Responsibilities</h6>
                        <ul class="text-slate-700 mb-0" style="font-size:0.88rem; line-height:1.8;">
                            <li>Verify physical presence of all tagged assets in your unit</li>
                            <li>Confirm asset condition and functionality</li>
                            <li>Report missing, damaged, or transferred items</li>
                            <li>Update system records with any changes</li>
                            <li>Cooperate with audit team during verification visits</li>
                        </ul>
                    </div>
                </div>
            </section>

            {{-- Maintenance --}}
            <section id="maintenance" class="mb-5 scroll-mt-5">
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                         style="background:linear-gradient(to right, #f0fdf4, #dcfce7);">
                        <h4 class="fw-black text-slate-900 mb-0 d-flex align-items-center gap-2" style="font-size:1.2rem;">
                            <div class="rounded-circle bg-emerald-600 d-flex align-items-center justify-content-center text-white"
                                 style="width:32px; height:32px; font-size:0.9rem;">
                                7
                            </div>
                            Maintenance & Disposal
                        </h4>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="rounded-3 p-4 h-100" style="background:#fffbeb; border:2px solid #f59e0b;">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <i class="fas fa-wrench text-amber-600" style="font-size:1.5rem;"></i>
                                        <h6 class="fw-bold text-amber-900 mb-0" style="font-size:0.95rem;">Under Maintenance</h6>
                                    </div>
                                    <p class="text-slate-700 mb-3" style="font-size:0.85rem;">
                                        Use this status for assets undergoing repair or servicing.
                                    </p>
                                    <ul class="text-slate-700 mb-0" style="font-size:0.78rem;">
                                        <li>Submit service request through system</li>
                                        <li>Document service provider details</li>
                                        <li>Update status when returned</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="rounded-3 p-4 h-100" style="background:#fef2f2; border:2px solid #ef4444;">
                                    <div class="d-flex align-items-center gap-2 mb-3">
                                        <i class="fas fa-trash text-rose-600" style="font-size:1.5rem;"></i>
                                        <h6 class="fw-bold text-rose-900 mb-0" style="font-size:0.95rem;">Disposal Process</h6>
                                    </div>
                                    <p class="text-slate-700 mb-3" style="font-size:0.85rem;">
                                        For obsolete or non-functional assets.
                                    </p>
                                    <ul class="text-slate-700 mb-0" style="font-size:0.78rem;">
                                        <li>Get unit head approval</li>
                                        <li>Submit disposal request</li>
                                        <li>Await audit team verification</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Best Practices --}}
            <section id="best-practices" class="mb-5 scroll-mt-5">
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                         style="background:linear-gradient(to right, #f0fdf4, #dcfce7);">
                        <h4 class="fw-black text-slate-900 mb-0 d-flex align-items-center gap-2" style="font-size:1.2rem;">
                            <div class="rounded-circle bg-emerald-600 d-flex align-items-center justify-content-center text-white"
                                 style="width:32px; height:32px; font-size:0.9rem;">
                                <i class="fas fa-star" style="font-size:0.85rem;"></i>
                            </div>
                            Best Practices
                        </h4>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-2 p-3 rounded-3 bg-emerald-50 border border-emerald-100">
                                    <i class="fas fa-check-circle text-emerald-600 mt-1"></i>
                                    <div>
                                        <div class="fw-bold text-slate-900 mb-1" style="font-size:0.85rem;">Be Specific</div>
                                        <div class="text-slate-600" style="font-size:0.78rem;">Include model numbers and detailed descriptions</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-2 p-3 rounded-3 bg-emerald-50 border border-emerald-100">
                                    <i class="fas fa-check-circle text-emerald-600 mt-1"></i>
                                    <div>
                                        <div class="fw-bold text-slate-900 mb-1" style="font-size:0.85rem;">Clear Photos</div>
                                        <div class="text-slate-600" style="font-size:0.78rem;">Upload high-quality, well-lit images</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-2 p-3 rounded-3 bg-emerald-50 border border-emerald-100">
                                    <i class="fas fa-check-circle text-emerald-600 mt-1"></i>
                                    <div>
                                        <div class="fw-bold text-slate-900 mb-1" style="font-size:0.85rem;">Prompt Updates</div>
                                        <div class="text-slate-600" style="font-size:0.78rem;">Report changes immediately to maintain accuracy</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-start gap-2 p-3 rounded-3 bg-emerald-50 border border-emerald-100">
                                    <i class="fas fa-check-circle text-emerald-600 mt-1"></i>
                                    <div>
                                        <div class="fw-bold text-slate-900 mb-1" style="font-size:0.85rem;">Regular Checks</div>
                                        <div class="text-slate-600" style="font-size:0.78rem;">Monitor your dashboard for pending actions</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- FAQ --}}
            <section id="faq" class="mb-5 scroll-mt-5">
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                         style="background:linear-gradient(to right, #f0fdf4, #dcfce7);">
                        <h4 class="fw-black text-slate-900 mb-0 d-flex align-items-center gap-2" style="font-size:1.2rem;">
                            <div class="rounded-circle bg-emerald-600 d-flex align-items-center justify-content-center text-white"
                                 style="width:32px; height:32px; font-size:0.9rem;">
                                <i class="fas fa-question" style="font-size:0.85rem;"></i>
                            </div>
                            Frequently Asked Questions
                        </h4>
                    </div>
                    <div class="p-4">
                        <div class="accordion" id="faqAccordion">
                            @php
                                $faqs = [
                                    ['How long does approval take?', 'Submissions are typically reviewed within 3-5 business days. Complex cases may take longer.'],
                                    ['Can I edit submitted items?', 'Yes, you can edit items with "Pending" status. Approved items require a new update submission.'],
                                    ['What if I lose an asset?', 'Report missing assets immediately to your unit head and through the system. An investigation will be initiated.'],
                                    ['How do I transfer assets?', 'Use the "Transfer" entry type and specify the receiving unit. Both units must confirm the transfer.'],
                                    ['What file formats are accepted?', 'JPG, PNG for images. PDF for documents. Maximum size: 10MB per file.'],
                                ];
                            @endphp

                            @foreach($faqs as $index => $faq)
                            <div class="accordion-item border-0 border-bottom">
                                <h2 class="accordion-header">
                                    <button class="accordion-button {{ $index == 0 ? '' : 'collapsed' }} bg-white fw-bold" 
                                            type="button" 
                                            data-bs-toggle="collapse" 
                                            data-bs-target="#faq{{ $index }}"
                                            style="font-size:0.88rem; padding:1rem 0;">
                                        <i class="fas fa-circle text-emerald-600 me-2" style="font-size:0.4rem;"></i>
                                        {{ $faq[0] }}
                                    </button>
                                </h2>
                                <div id="faq{{ $index }}" 
                                     class="accordion-collapse collapse {{ $index == 0 ? 'show' : '' }}" 
                                     data-bs-parent="#faqAccordion">
                                    <div class="accordion-body text-slate-700" style="font-size:0.85rem; padding:0 0 1rem 0;">
                                        {{ $faq[1] }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- Support --}}
            <section id="support" class="scroll-mt-5">
                <div class="bg-white rounded-4 border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 bg-gradient-to-r border-bottom border-slate-100"
                         style="background:linear-gradient(to right, #f0fdf4, #dcfce7);">
                        <h4 class="fw-black text-slate-900 mb-0 d-flex align-items-center gap-2" style="font-size:1.2rem;">
                            <div class="rounded-circle bg-emerald-600 d-flex align-items-center justify-content-center text-white"
                                 style="width:32px; height:32px; font-size:0.9rem;">
                                <i class="fas fa-headset" style="font-size:0.85rem;"></i>
                            </div>
                            Technical Support
                        </h4>
                    </div>
                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="rounded-3 p-4 border border-slate-200 bg-slate-50 text-center">
                                    <i class="fas fa-envelope text-emerald-600 mb-3" style="font-size:2rem;"></i>
                                    <h6 class="fw-bold text-slate-900 mb-2" style="font-size:0.9rem;">Email Support</h6>
                                    <p class="text-slate-600 mb-2" style="font-size:0.82rem;">College of Medicine IT Unit</p>
                                    <a href="mailto:support.itu@com.ui.edu.ng" class="fw-bold text-emerald-600 text-decoration-none" style="font-size:0.85rem;">
                                        support.itu@com.ui.edu.ng
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="rounded-3 p-4 border border-slate-200 bg-slate-50 text-center">
                                    <i class="fas fa-phone text-emerald-600 mb-3" style="font-size:2rem;"></i>
                                    <h6 class="fw-bold text-slate-900 mb-2" style="font-size:0.9rem;">Phone Support</h6>
                                    <p class="text-slate-600 mb-2" style="font-size:0.82rem;">Office Hours: Mon-Fri, 9AM-5PM</p>
                                    <p class="fw-bold text-emerald-600 mb-0" style="font-size:0.85rem;">
                                        +234 (0) 123 456 7890
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-3 p-4 mt-4" style="background:linear-gradient(135deg, #f0fdf4, #dcfce7);">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-info-circle text-emerald-600" style="font-size:1.5rem;"></i>
                                <div>
                                    <div class="fw-bold text-slate-900 mb-1" style="font-size:0.9rem;">Need Help?</div>
                                    <p class="text-slate-700 mb-0" style="font-size:0.82rem;">
                                        For urgent issues or system errors, contact the IT Unit immediately. Include your username and a detailed description of the problem.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<style>
html { scroll-behavior: smooth; }
.scroll-mt-5 { scroll-margin-top: 5rem; }

#manual-nav .list-group-item {
    border: none;
    transition: all 0.2s;
}

#manual-nav .list-group-item:hover {
    background: #f0fdf4;
    color: #059669;
}

#manual-nav .list-group-item:hover i {
    color: #059669;
}

#manual-nav .list-group-item.active {
    background: linear-gradient(135deg, #059669, #047857);
    color: white;
    border-radius: 10px;
    margin: 2px 0;
}

#manual-nav .list-group-item.active i {
    color: white;
}

.accordion-button:not(.collapsed) {
    color: #059669;
    background-color: #f0fdf4;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: transparent;
}

@media print {
    .sticky-top, button, .d-print-none { display: none !important; }
    .bg-white { box-shadow: none !important; }
}
</style>

<script>
window.addEventListener('DOMContentLoaded', () => {
    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            const id = entry.target.getAttribute('id');
            const link = document.querySelector(`#manual-nav a[href="#${id}"]`);
            if (link && entry.intersectionRatio > 0) {
                document.querySelectorAll('#manual-nav a').forEach(a => a.classList.remove('active'));
                link.classList.add('active');
            }
        });
    }, { rootMargin: '-20% 0px -70% 0px' });

    document.querySelectorAll('section[id]').forEach((section) => {
        observer.observe(section);
    });
});
</script>

@endsection