@extends('layouts.admin')

@section('title', 'Registry Control Center')

@push('styles')
<style>
    :root { --admin-orange: #f97316; }
    
    /* Standardized Admin Typography */
    body { font-size: 0.85rem; color: #334155; }
    .text-label { font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.025em; color: #64748b; }
    
    .config-card { transition: border-color 0.2s ease; border: 1px solid #e2e8f0; }
    .config-card:hover { border-color: var(--admin-orange); }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-slate-50 pb-12">
    
    {{-- Header Section --}}
    <header class="bg-white border-b border-slate-200 py-4 mb-8">
        <div class="max-w-[1400px] mx-auto px-6 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="text-lg font-black text-slate-800 uppercase tracking-tight mb-0">Registry Control Center</h1>
                <p class="text-[10px] font-bold text-orange-500 uppercase tracking-widest">Global Asset & Category Management</p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold px-4 py-2 rounded-lg" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                    <i class="fas fa-plus mr-1"></i> New Category
                </button>
            </div>
        </div>
    </header>

    <div class="max-w-[1400px] mx-auto px-6">
        
        {{-- 1. Global Intelligence (Faculties, Depts, Units, e.t.c) --}}
        <div class="row g-4 mb-8">
            <div class="col-lg-8">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200 h-full">
                    <h4 class="text-label mb-4">Registry Breakdown by Entity</h4>
                    <div class="row g-3">
                        @php
                            $entities = [
                                ['Faculties', $dropdownData['faculties']->count() ?? 0, 'fa-graduation-cap'],
                                ['Departments', $dropdownData['departments']->count() ?? 0, 'fa-university'],
                                ['Offices', $dropdownData['offices']->count() ?? 0, 'fa-door-open'],
                                ['Units', $dropdownData['units']->count() ?? 0, 'fa-building'],
                                ['Institutes', $dropdownData['institutes']->count() ?? 0, 'fa-microscope'],
                            ];
                        @endphp
                        @foreach($entities as [$name, $count, $icon])
                        <div class="col-md-2 col-6 text-center">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <i class="fas {{ $icon }} text-orange-500 mb-2"></i>
                                <div class="text-[10px] font-bold text-slate-400 uppercase">{{ $name }}</div>
                                <div class="text-base font-black text-slate-800">{{ $count }}</div>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-md-2 col-12">
                            <div class="p-3 bg-orange-500 rounded-xl text-white text-center">
                                <i class="fas fa-cubes mb-2"></i>
                                <div class="text-[10px] font-bold uppercase opacity-80">Total Items</div>
                                <div class="text-base font-black">{{ $extraData['totalItemsCount'] ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 2. Status Breakdown (Doughnut) --}}
            <div class="col-lg-4">
                <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-200">
                    <h4 class="text-label mb-4">Asset Status Mix</h4>
                    <div class="h-40"><canvas id="statusDoughnut"></canvas></div>
                </div>
            </div>
        </div>

        {{-- 3. Management Panels --}}
        <div class="row g-4">
            {{-- Category & Subcategory Manager --}}
            <div class="col-md-7">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 d-flex justify-content-between align-items-center">
                        <h4 class="text-label mb-0">Asset Categories & Subcategories</h4>
                        <span class="text-[9px] font-black text-orange-600 uppercase tracking-widest">Verified Schema</span>
                    </div>
                    <div class="p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase">
                                    <tr>
                                        <th class="px-5 py-3">Category Name</th>
                                        <th>Subcategories</th>
                                        <th class="text-end px-5">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($dropdownData['categories'] as $cat)
                                    <tr>
                                        <td class="px-5 py-3 font-bold text-slate-700">{{ $cat->category_name }}</td>
                                        <td>
                                            @foreach($cat->subcategories as $sub)
                                                <span class="badge bg-slate-100 text-slate-600 border border-slate-200 text-[9px] uppercase mr-1">{{ $sub->subcategory_name }}</span>
                                            @endforeach
                                            <button class="btn btn-link text-orange-600 text-[10px] font-bold p-0 no-underline">+ Add Sub</button>
                                        </td>
                                        <td class="text-end px-5">
                                            <button class="btn btn-sm text-slate-400 hover:text-orange-500"><i class="fas fa-edit"></i></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submission Types (Condition/Workflow Manager) --}}
            <div class="col-md-5">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <h4 class="text-label mb-4">Submission Types & Conditions</h4>
                    <div class="d-flex flex-column gap-3">
                        @php
                            $types = [
                                ['New Purchase', 'Assets recently acquired via procurement', 'fa-shopping-cart', 'text-emerald-600', 'bg-emerald-50'],
                                ['Transfer', 'Internal movement between departments', 'fa-exchange-alt', 'text-blue-600', 'bg-blue-50'],
                                ['Maintenance', 'Items currently under repair/service', 'fa-tools', 'text-orange-600', 'bg-orange-50'],
                                ['Disposal', 'Items marked for retirement or auction', 'fa-trash-alt', 'text-rose-600', 'bg-rose-50']
                            ];
                        @endphp
                        @foreach($types as [$title, $desc, $icon, $tColor, $bgColor])
                        <div class="d-flex align-items-center justify-content-between p-3 rounded-xl border border-slate-100 hover:bg-slate-50 transition-all cursor-pointer">
                            <div class="d-flex align-items-center gap-3">
                                <div class="w-10 h-10 rounded-lg {{ $bgColor }} {{ $tColor }} flex items-center justify-center">
                                    <i class="fas {{ $icon }}"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-black text-slate-800 uppercase">{{ $title }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $desc }}</div>
                                </div>
                            </div>
                            <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
                        </div>
                        @endforeach
                    </div>
                    <button class="btn w-full mt-4 border-2 border-dashed border-slate-200 text-slate-400 text-[10px] font-bold uppercase py-2 hover:border-orange-300 hover:text-orange-500 transition-all">
                        + Add Custom Submission Logic
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Category Modal --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-2xl border-0 shadow-lg">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0 px-5 pt-5 pb-0">
                    <h5 class="text-label">Create New Asset Category</h5>
                </div>
                <div class="modal-body px-5 py-4">
                    <div class="mb-3">
                        <label class="text-[10px] font-bold text-slate-400 uppercase mb-2 block">Category Name</label>
                        <input type="text" name="category_name" class="form-control bg-slate-50 border-slate-200 text-sm rounded-lg py-2" placeholder="e.g. Laboratory Equipment">
                    </div>
                </div>
                <div class="modal-footer border-0 px-5 pb-5 pt-0">
                    <button type="button" class="btn text-xs font-bold text-slate-400 uppercase" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn bg-orange-500 text-white text-xs font-bold uppercase px-4 rounded-lg">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('statusDoughnut').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Approved', 'Pending', 'Rejected'],
                datasets: [{
                    data: [{{ $extraData['countNew'] ?? 10 }}, 5, 2], // Replace with real stats
                    backgroundColor: ['#10b981', '#f97316', '#ef4444'],
                    borderWidth: 0,
                }]
            },
            options: {
                cutout: '80%',
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { boxWidth: 10, font: { size: 9, weight: 'bold' } } }
                }
            }
        });
    });
</script>
@endpush