@extends('layouts.staff')

@section('title', 'Staff Guidelines & Manual')

@section('content')
<style>
    /* 1. Fix for Sticky behavior */
    .main-wrapper { overflow: visible !important; }

    /* 2. Desktop Sidebar behavior */
    @media (min-width: 992px) {
        .sticky-sidebar {
            position: sticky;
            top: 100px;
            z-index: 100;
        }
    }

    /* 3. MOBILE RESPONSIVENESS: Horizontal Scroll Table of Contents */
    @media (max-width: 991.98px) {
        .mobile-nav-scroll {
            display: flex !important;
            flex-direction: row !important;
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 5px;
        }
        .mobile-nav-scroll .list-group-item {
            border: 1px solid #dee2e6 !important;
            margin-right: 8px;
            border-radius: 8px !important;
            padding: 8px 16px !important;
        }
        .card-header-mobile { display: none; } /* Hide "Table of Contents" text on mobile */
    }

    .step-circle {
        width: 32px; height: 32px;
        background: #0d6efd; color: #fff;
        border-radius: 50%; display: flex;
        align-items: center; justify-content: center;
        flex-shrink: 0; font-weight: bold; font-size: 0.85rem;
    }

    html { scroll-behavior: smooth; }
    
    .list-group-item.active {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: white !important;
    }
</style>

<div class="container-fluid px-3 px-md-4 py-4 main-wrapper">
    <div class="mb-4">
        <h2 class="fw-bold text-dark">Staff Guidelines & Manual</h2>
        <p class="text-muted">Official procedures for College of Medicine inventory tracking.</p>
    </div>

    <div class="row">
        <div class="col-lg-3 mb-4">
            <div class="sticky-sidebar">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom-0 card-header-mobile">
                        <h6 class="mb-0 fw-bold text-muted small uppercase">TABLE OF CONTENTS</h6>
                    </div>
                    <div class="list-group list-group-flush mobile-nav-scroll" id="manual-nav">
                        <a href="#introduction" class="list-group-item list-group-item-action border-0 py-3">
                            <i class="fas fa-info-circle me-2"></i> Intro
                        </a>
                        <a href="#submissions" class="list-group-item list-group-item-action border-0 py-3">
                            <i class="fas fa-plus-square me-2"></i> Submissions
                        </a>
                        <a href="#tagging" class="list-group-item list-group-item-action border-0 py-3">
                            <i class="fas fa-tag me-2"></i> Tagging
                        </a>
                        <a href="#audit" class="list-group-item list-group-item-action border-0 py-3">
                            <i class="fas fa-check-double me-2"></i> Audit
                        </a>
                        <a href="#maintenance" class="list-group-item list-group-item-action border-0 py-3">
                            <i class="fas fa-tools me-2"></i> Service
                        </a>
                        <a href="#support" class="list-group-item list-group-item-action border-0 py-3">
                            <i class="fas fa-headset me-2"></i> Support
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 p-md-5">
                    
                    <section id="introduction" class="mb-5 pt-2">
                        <h4 class="fw-bold mb-4 border-start border-primary border-4 ps-3">1. Introduction</h4>
                        <p>This system tracks medical equipment and furniture across <strong>Faculties, Departments, and Units</strong> within the College of Medicine.</p>
                        <div class="alert alert-primary bg-light border-0 d-flex align-items-center rounded-3 shadow-sm">
                            <i class="fas fa-hospital-symbol me-3 fa-2x opacity-50 text-primary"></i>
                            <div><strong>Objective:</strong> To maintain an accurate digital twin of all physical assets for audit readiness.</div>
                        </div>
                    </section>

                    <section id="submissions" class="mb-5 pt-2">
                        <h4 class="fw-bold mb-4 border-start border-primary border-4 ps-3">2. Making New Submissions</h4>
                        <p>Follow these steps to log new items:</p>
                        <div class="d-flex align-items-start mb-3">
                            <div class="step-circle me-3">1</div>
                            <div>Navigate to <strong>New Submission</strong> in the sidebar menu.</div>
                        </div>
                        <div class="d-flex align-items-start mb-3">
                            <div class="step-circle me-3">2</div>
                            <div>Fill in the Item Name and select the specific <strong>Funding Source</strong>.</div>
                        </div>
                        <div class="d-flex align-items-start">
                            <div class="step-circle me-3">3</div>
                            <div>Attach a photo of the item or the receipt for verification.</div>
                        </div>
                    </section>

                    <section id="tagging" class="mb-5 pt-2">
                        <h4 class="fw-bold mb-4 border-start border-primary border-4 ps-3">3. Asset Tagging</h4>
                        <p>All items must have a physical barcoded tag. If your item shows <strong>"Tag Pending"</strong>, contact the unit head to receive your physical sticker.</p>
                    </section>

                    <section id="audit" class="mb-5 pt-2">
                        <h4 class="fw-bold mb-4 border-start border-primary border-4 ps-3">4. Annual Audit Verification</h4>
                        <p>Twice a year, you must verify that all items assigned to your department are still physically present and functional.</p>
                    </section>

                    <section id="maintenance" class="mb-5 pt-2">
                        <h4 class="fw-bold mb-4 border-start border-primary border-4 ps-3">5. Maintenance & Disposal</h4>
                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="p-3 border rounded bg-light shadow-sm">
                                    <h6 class="fw-bold text-warning"><i class="fas fa-wrench me-2"></i>Under Maintenance</h6>
                                    <p class="small mb-0 text-muted">For equipment being repaired by the technical unit.</p>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="p-3 border rounded bg-light shadow-sm">
                                    <h6 class="fw-bold text-danger"><i class="fas fa-trash me-2"></i>Retired / Obsolete</h6>
                                    <p class="small mb-0 text-muted">For assets no longer fit for medical or office use.</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section id="support" class="mb-0 pt-2">
                        <h4 class="fw-bold mb-4 border-start border-primary border-4 ps-3">6. Technical Support</h4>
                        <div class="p-4 bg-light rounded-3 border d-flex flex-column flex-sm-row align-items-center shadow-sm">
                            <i class="fas fa-envelope-open-text fa-2x text-primary mb-3 mb-sm-0 me-sm-4"></i>
                            <div class="text-center text-sm-start">
                                <div class="fw-bold">College of Medicine IT Unit</div>
                                <div class="text-muted small">support.itu@com.ui.edu.ng</div>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                const id = entry.target.getAttribute('id');
                const link = document.querySelector(`#manual-nav a[href="#${id}"]`);
                if (entry.intersectionRatio > 0) {
                    document.querySelectorAll('#manual-nav a').forEach(a => a.classList.remove('active'));
                    link.classList.add('active');
                }
            });
        }, { rootMargin: '-20% 0px -70% 0px' }); // Better detection for active section

        document.querySelectorAll('section[id]').forEach((section) => {
            observer.observe(section);
        });
    });
</script>
@endsection