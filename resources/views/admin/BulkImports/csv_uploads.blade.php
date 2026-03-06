@extends('layouts.admin')

@section('title', 'Upload Assets')

@section('content')

<div class="mx-auto my-5 max-w-[1300px] rounded-[60px] border border-white/10 bg-gradient-to-br from-[#0f172a] via-[#1e293b] to-[#334155] p-12 shadow-[0_40px_100px_rgba(0,0,0,0.4)]">
    
    {{-- Alert System --}}
    @foreach(['success' => ['bg-emerald-500', 'fa-check-circle'], 'error' => ['bg-red-600', 'fa-exclamation-circle'], 'info' => ['bg-blue-600', 'fa-info-circle']] as $type => $meta)
        @if(session($type))
            <div class="alert alert-dismissible fade show border-0 rounded-4 p-4 mb-4 d-flex align-items-center shadow-lg animate-[slideDown_0.5s_ease-out] backdrop-blur-md" 
                 style="background: {{ $type == 'success' ? '#05966915' : ($type == 'error' ? '#dc262615' : '#2563eb15') }}; border-left: 4px solid {{ $type == 'success' ? '#059669' : ($type == 'error' ? '#dc2626' : '#2563eb') }} !important;">
                <div class="rounded-circle d-flex align-items-center justify-content-center me-3 h-12 w-12" style="background: rgba(255,255,255,0.1);">
                    <i class="fas {{ $meta[1] }} text-white text-xl"></i>
                </div>
                <div class="text-white">
                    <div class="fw-bold mb-1">{{ ucfirst($type) }}</div>
                    <div class="text-xs opacity-90">{{ session($type) }}</div>
                </div>
                <button type="button" class="btn-close btn-close-white ms-auto shadow-none" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="rounded-[40px] border border-white/10 bg-white/5 p-10 shadow-2xl backdrop-blur-[25px]">
        
        <div class="mb-8">
            <a href="{{ route('admin.bulk-assets.index') }}" 
               class="group relative inline-flex items-center gap-3 overflow-hidden rounded-2xl border border-white/10 bg-white/5 px-6 py-3 text-slate-400 no-underline backdrop-blur-md transition-all duration-300 hover:border-amber-500/40 hover:bg-white/10 hover:text-slate-100 hover:-translate-y-1">
                <i class="fas fa-arrow-left text-sm transition-transform duration-300 group-hover:-translate-x-1 group-hover:text-amber-500"></i>
                <span class="text-l text-white font-bold tracking-wide">Back</span>
                <div class="absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/5 to-transparent transition-transform duration-500 group-hover:translate-x-full"></div>
            </a>
        </div>

        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4 h-[100px] w-[100px] bg-gradient-to-br from-amber-400 to-amber-700 shadow-[0_20px_60px_rgba(245,158,11,0.3)]">
                 <i class="fas fa-cloud-upload-alt text-white text-4xl"></i>
            </div>
            <h1 class="text-white fw-black mb-2 text-4xl lg:text-5xl">Upload Asset Data</h1>
            <p class="text-white/70 text-lg">Import multiple assets at once using CSV or Excel files</p>
        </div>

        <form action="{{ route('admin.bulk-assets.csv.process') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
            @csrf

            <div class="row g-5">
                <div class="col-lg-5">
                    <div class="mb-5">
                        <h5 class="text-white fw-bold mb-4">
                            <i class="fas fa-sitemap text-amber-500 me-2"></i> Destination
                        </h5>
                        
                        <div class="mb-4">
                            <label class="form-label text-white-50 small fw-bold tracking-wider">SELECT DESTINATION</label>
                            <select name="entity_type" id="entity_type" 
                                    class="form-select form-select-lg rounded-3 shadow-none bg-black/30 border-white/10 text-slate-400 transition-all focus:border-amber-500 focus:bg-black/40 focus:text-white" required>
                                <option value="" class="bg-[#1e293b]">Select Destination...</option>
                                <option value="faculty" class="bg-[#1e293b]">Faculty</option>
                                <option value="office" class="bg-[#1e293b]">Office</option>
                                <option value="department" class="bg-[#1e293b]">Department</option>
                                <option value="unit" class="bg-[#1e293b]">Unit</option>
                                <option value="institute" class="bg-[#1e293b]">Institute</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-white-50 small fw-bold tracking-wider">ASSIGN TO NAME</label>
                            <select name="entity_id" id="entity_id" 
                                    class="form-select form-select-lg rounded-3 shadow-none bg-black/40 border-white/5 text-slate-600 transition-all disabled:cursor-not-allowed disabled:opacity-100" 
                                    required disabled>
                                <option value="" class="bg-[#1e293b]">Awaiting destination selection...</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-4 rounded-4 bg-emerald-500/10 border border-emerald-500/20">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-emerald-600 p-3 rounded-3 shadow-lg shadow-emerald-900/40">
                                <i class="fas fa-file-csv text-white text-2xl"></i>
                            </div>
                            <div>
                                <h6 class="text-white fw-bold mb-0">CSV Template</h6>
                                <p class="text-emerald-500/60 small mb-0 font-bold">Standardized Format</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.bulk-assets.csv.template') }}" class="btn btn-success w-100 rounded-pill fw-bold py-3 bg-emerald-600 border-0 hover:bg-emerald-500 transition-all shadow-lg">
                            <i class="fas fa-download me-2"></i> Download Template
                        </a>
                    </div>
                </div>

                <div class="col-lg-7">
                    <h5 class="text-white fw-bold mb-4"><i class="fas fa-file-import text-amber-500 me-2"></i> Upload File</h5>

                    <div id="dropZone" 
                         class="group rounded-4 p-5 text-center border-2 border-dashed border-white/20 mb-4 transition-all bg-black/20 hover:bg-black/40 hover:border-amber-500/40 cursor-pointer"
                         onclick="document.getElementById('import_file').click()">
                        
                        <input type="file" name="import_file" id="import_file" class="d-none" accept=".csv, .xlsx, .xls" required>
                        
                        <div id="dropPrompt">
                            <i class="fas fa-folder-open text-white-50 text-5xl mb-3 transition-colors group-hover:text-amber-500"></i>
                            <h5 class="text-white fw-bold">Drop your file here or click to browse</h5>
                            <p class="text-white/60 small uppercase tracking-widest">Excel or CSV (Max 10MB)</p>
                        </div>

                        <div id="fileInfo" class="d-none animate-bounce-short">
                            <div class="bg-indigo-500/20 border border-indigo-500/40 px-4 py-2 rounded-pill d-inline-flex align-items-center">
                                <i class="fas fa-file-excel text-indigo-400 me-3"></i>
                                <span id="fileName" class="text-white fw-bold small me-4"></span>
                                <i class="fas fa-times text-white/50 cursor-pointer hover:text-white" onclick="event.stopPropagation(); clearFile()"></i>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-4 overflow-hidden border border-white/10 bg-white/5">
                        <div class="p-4 d-flex align-items-center justify-content-between text-white cursor-pointer hover:bg-white/5 transition-colors" 
                             onclick="toggleFormatTable()" id="formatToggleHeader">
                            <div class="d-flex align-items-center gap-3">
                                <i class="fas fa-table text-amber-500"></i>
                                <div>
                                    <h6 class="fw-bold mb-0">Expected File Format</h6>
                                    <p class="mb-0 text-xs text-white-50">View all required and optional columns</p>
                                </div>
                            </div>
                            <i id="chevron" class="fas fa-chevron-down text-white-50 transition-transform duration-300"></i>
                        </div>

                        <div id="formatTable" class="hidden p-4 bg-black/30 border-top border-white/5">
                            <div class="table-responsive">
                                <table class="table table-dark table-borderless table-sm mb-0 align-middle">
                                    <thead>
                                        <tr class="text-amber-500 text-xs uppercase tracking-wider border-bottom border-white/10">
                                            <th class="pb-2">Column Header</th>
                                            <th class="pb-2">Status</th>
                                            <th class="pb-2">Requirements</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-xs">
                                        <tr><td class="pt-3 fw-bold font-mono text-slate-200">item_name</td><td class="pt-3"><span class="badge bg-danger">Required</span></td><td class="pt-3">Full name of the asset</td></tr>
                                        <tr><td class="fw-bold font-mono text-slate-200">category_name</td><td><span class="badge bg-danger">Required</span></td><td>Must exist in system</td></tr>
                                        <tr><td class="fw-bold font-mono text-slate-200">subcategory_name</td><td><span class="badge bg-danger">Required</span></td><td>Must exist in system</td></tr>
                                        <tr><td class="fw-bold font-mono text-slate-200">quantity</td><td><span class="badge bg-danger">Required</span></td><td>Numeric value only</td></tr>
                                        <tr><td class="fw-bold font-mono text-slate-200">unit_price</td><td><span class="badge bg-danger">Required</span></td><td>Currency format (₦)</td></tr>
                                        <tr><td class="fw-bold font-mono text-slate-200">status</td><td><span class="badge bg-danger">Required</span></td><td>Available, Assigned, etc.</td></tr>
                                        <tr><td class="fw-bold font-mono text-slate-200 text-slate-400">serial_number</td><td><span class="badge bg-secondary opacity-50">Optional</span></td><td>Unique device ID</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5 gap-2 justify-content-center">
                        <a href="{{ route('admin.bulk-assets.index') }}" class="btn rounded-pill px-4 py-3 fw-bold text-white border border-white/10 bg-white/5 hover:bg-white/10 transition-colors">Cancel</a>
                        <button type="submit" class="btn rounded-pill px-3 py-2 fw-bold shadow-xl text-white bg-emerald-600 hover:bg-emerald-500 border-0 transition-all hover:-translate-y-1" id="submitBtn">
                            <span id="btnText">Upload & Import Assets</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Logic for toggling instructions table
    function toggleFormatTable() {
        const table = document.getElementById('formatTable');
        const chevron = document.getElementById('chevron');
        
        if (table.classList.contains('hidden')) {
            table.classList.remove('hidden');
            chevron.classList.add('rotate-180');
        } else {
            table.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }

    // Dynamic Select Loading
    document.getElementById('entity_type').addEventListener('change', function() {
        const type = this.value;
        const idSelect = document.getElementById('entity_id');
        idSelect.innerHTML = '<option value="">Loading...</option>';
        idSelect.disabled = true;

        if (!type) {
            idSelect.innerHTML = '<option value="">Awaiting destination...</option>';
            return;
        }

        const data = {
            faculty: @json($faculties),
            department: @json($departments),
            office:@json($offices),
            unit: @json($units),
            institute: @json($institutes)
        };

        const list = data[type] || [];
        let html = `<option value="" class="bg-[#1e293b]">Select ${type.charAt(0).toUpperCase() + type.slice(1)}</option>`;
        
        list.forEach(item => {
            const id = item[type + '_id'];
            const name = (type === 'department' ? item.dept_name : item[type + '_name']);
            html += `<option value="${id}" class="bg-[#1e293b]">${name}</option>`;
        });

        idSelect.innerHTML = html;
        idSelect.disabled = false;
        // Visual switch from "Dark Disabled" to "Active Slate"
        idSelect.classList.replace('text-slate-600', 'text-slate-400');
    });

    // File Input UI Sync
    document.getElementById('import_file').addEventListener('change', function(e) {
        if (e.target.files[0]) {
            document.getElementById('fileName').textContent = e.target.files[0].name;
            document.getElementById('fileInfo').classList.remove('d-none');
            document.getElementById('dropPrompt').classList.add('d-none');
        }
    });

    window.clearFile = function() {
        document.getElementById('import_file').value = '';
        document.getElementById('fileInfo').classList.add('d-none');
        document.getElementById('dropPrompt').classList.remove('d-none');
    };

    // Form Submission UI feedback
    document.getElementById('uploadForm').addEventListener('submit', function() {
        const btn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        btn.disabled = true;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
        btnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing File...';
    });
</script>
@endpush
@endsection