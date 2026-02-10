@extends('layouts.admin')

@section('title', 'Register New User')

@section('content')

<div class="min-vh-100 py-4 px-3 px-lg-5 bg-slate-50">
<div style="max-width:1400px;" class="mx-auto">

    {{-- Header with breadcrumb + actions --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.users.index') }}" 
               class="btn btn-white border border-slate-200 rounded-circle shadow-sm d-flex align-items-center justify-content-center"
               style="width:45px; height:45px; transition:all 0.2s;"
               onmouseenter="this.style.background='#0f172a'; this.style.borderColor='#0f172a'; this.querySelector('i').style.color='#fff';"
               onmouseleave="this.style.background='#fff'; this.style.borderColor='#e2e8f0'; this.querySelector('i').style.color='#94a3b8';">
                <i class="fas fa-arrow-left text-slate-400" style="transition:color 0.2s;"></i>
            </a>
            <div>
                <h3 class="fw-black text-slate-900 mb-1" style="font-size:1.5rem; letter-spacing:-0.02em;">Register New User</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 bg-transparent p-0" style="font-size:0.8rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none text-slate-500">Users</a></li>
                        <li class="breadcrumb-item active text-slate-900 fw-bold">Create Account</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Save button (visible on desktop) --}}
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-white border-slate-200 text-slate-600 fw-bold d-none d-md-flex align-items-center px-4" style="border-radius:10px; font-size:0.8rem;">Cancel</a>
            <button type="submit" form="createUserForm" 
                    class="btn text-white fw-black d-none d-md-flex align-items-center gap-2 rounded-3 shadow-lg"
                    style="background:linear-gradient(135deg, #f59e0b, #d97706); font-size:0.8rem; letter-spacing:0.06em; text-transform:uppercase; padding:12px 28px; transition:transform 0.2s, box-shadow 0.2s;"
                    onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(245,158,11,0.4)';"
                    onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(245,158,11,0.3) ';">
                <i class="fas fa-user-plus"></i> Register User
            </button>
        </div>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
    <div class="rounded-4 mb-4 p-4 bg-danger bg-opacity-10 border border-danger border-opacity-25">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="fas fa-exclamation-triangle text-danger"></i>
            <span class="fw-black text-danger-emphasis" style="font-size:0.88rem;">Please correct the following errors:</span>
        </div>
        <ul class="mb-0 text-danger-emphasis" style="font-size:0.82rem; line-height:1.8;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row g-4">
        {{-- LEFT COLUMN: Form --}}
        <div class="col-lg-8">
            <form action="{{ route('admin.users.store') }}" method="POST" id="createUserForm">
                @csrf

                <div class="rounded-4 bg-white border border-slate-200 shadow-sm overflow-hidden mb-4">
                    {{-- Section: Identity & Access --}}
                    <div class="p-4 p-md-5 border-bottom border-slate-100">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div class="rounded-2 d-flex align-items-center justify-content-center bg-indigo-50" style="width:32px; height:32px;">
                                <i class="fas fa-id-card text-indigo-600" style="font-size:0.9rem;"></i>
                            </div>
                            <h6 class="fw-black text-slate-900 mb-0 text-uppercase" style="font-size:0.75rem; letter-spacing:0.15em;">Identity & Access</h6>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">Full Name</label>
                                <input type="text" name="full_name" 
                                       class="form-control border-slate-200 bg-slate-50 @error('full_name') border-danger @enderror" 
                                       placeholder="Enter full name"
                                       style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;"
                                       value="{{ old('full_name', $user->profile->full_name ?? '') }}" 
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">Username</label>
                                <input type="text" name="username" 
                                       class="form-control border-slate-200 bg-slate-50 @error('username') border-danger @enderror" 
                                       placeholder="unique_username"
                                       style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;"
                                       value="{{ old('username') }}" 
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">Email Address</label>
                                <input type="email" name="email" 
                                       class="form-control border-slate-200 bg-slate-50 @error('email') border-danger @enderror" 
                                       placeholder="email@example.com"
                                       style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;"
                                       value="{{ old('email') }}" 
                                       required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">Initial Account Status</label>
                                <select name="status" class="form-select border-slate-200 bg-slate-50" style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Organization & Role --}}
                    <div class="p-4 p-md-5 border-bottom border-slate-100">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div class="rounded-2 d-flex align-items-center justify-content-center bg-emerald-50" style="width:32px; height:32px;">
                                <i class="fas fa-sitemap text-emerald-600" style="font-size:0.9rem;"></i>
                            </div>
                            <h6 class="fw-black text-slate-900 mb-0 text-uppercase" style="font-size:0.75rem; letter-spacing:0.15em;">Organization & Role</h6>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">System Role</label>
                            <select name="role_id" class="form-select border-slate-200 bg-slate-50" style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;" required>
                                <option value="" selected disabled>Select a role...</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}" {{ old('role_id') == $role->role_id ? 'selected' : '' }}>{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Primary Affiliation Cards --}}
                        {{-- Faculty & Department Pair --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label text-slate-600 fw-bold text-uppercase mb-2" style="font-size:0.68rem; letter-spacing:0.08em;">Faculty</label>
                                <select name="faculty_id" id="faculty_select" class="form-select border-slate-200 bg-white" style="padding:0.6rem 0.9rem; border-radius:8px; font-size:0.84rem;">
                                    <option value="">None</option>
                                    @foreach($faculties as $faculty)
                                        <option value="{{ $faculty->faculty_id }}">{{ $faculty->faculty_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-slate-700 fw-bold mb-2 d-flex align-items-center gap-2" style="font-size:0.82rem; white-space: nowrap;">
                                    Department 
                                    <span class="text-slate-500 fw-normal" style="font-size: 0.75rem;">(Filtered by Faculty)</span>
                                </label>
                                <select name="dept_id" id="dept_select" class="form-select border-slate-200 bg-slate-50" style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;">
                                    <option value="" data-faculty="">None</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->dept_id }}" data-faculty="{{ $dept->faculty_id }}">
                                            {{ $dept->dept_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="border-slate-100 mb-4">

                        {{-- Office & Unit Pair --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-slate-600 fw-bold text-uppercase mb-2" style="font-size:0.68rem; letter-spacing:0.08em;">Office</label>
                                <select name="office_id" id="office_select" class="form-select border-slate-200 bg-white" style="padding:0.6rem 0.9rem; border-radius:8px; font-size:0.84rem;">
                                    <option value="">None</option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->office_id }}">{{ $office->office_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">Unit <span class="text-slate-500 fw-normal">(Filtered by Office)</span></label>
                                <select name="unit_id" id="unit_select" class="form-select border-slate-200 bg-slate-50" style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;">
                                    <option value="" data-office="">None</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->unit_id }}" data-office="{{ $unit->office_id }}">
                                            {{ $unit->unit_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Contact Information --}}
                    <div class="p-4 p-md-5">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div class="rounded-2 d-flex align-items-center justify-content-center bg-amber-50" style="width:32px; height:32px;">
                                <i class="fas fa-phone text-amber-600" style="font-size:0.9rem;"></i>
                            </div>
                            <h6 class="fw-black text-slate-900 mb-0 text-uppercase" style="font-size:0.75rem; letter-spacing:0.15em;">Contact Information</h6>
                        </div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">Phone Number</label>
                                <input type="text" name="phone" class="form-control border-slate-200 bg-slate-50" style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;" value="{{ old('phone') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">Address</label>
                                <textarea name="address" class="form-control border-slate-200 bg-slate-50" style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;" rows="2">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- RIGHT COLUMN: Info Panel --}}
        <div class="col-lg-4">
            <div class="rounded-4 bg-white border border-slate-200 shadow-sm overflow-hidden mb-4">
                <div class="p-3 bg-slate-50 border-bottom border-slate-200">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-info-circle text-slate-600" style="font-size:0.9rem;"></i>
                        <h6 class="fw-black text-slate-900 mb-0 text-uppercase" style="font-size:0.72rem; letter-spacing:0.12em;">Registration Info</h6>
                    </div>
                </div>
                <div class="p-4">
                    <p class="text-slate-600 mb-3" style="font-size:0.8rem; line-height:1.6;">
                        New accounts are assigned a <strong>default password</strong> based on their username + year.
                    </p>
                    <div class="rounded-3 p-3 bg-amber-50 border border-amber-100 mb-3">
                        <p class="text-amber-900 fw-bold mb-1" style="font-size:0.75rem;">Password Example:</p>
                        <code class="text-amber-700" style="font-size:0.85rem;">username@2026</code>
                    </div>
                    <p class="text-slate-500 mb-0" style="font-size:0.73rem;">
                        <i class="fas fa-shield-alt me-1"></i> Users will be prompted to change their password upon their first successful login.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sticky Mobile Save Button --}}
    <div class="d-md-none position-fixed bottom-0 start-0 end-0 p-3 bg-white border-top border-slate-200 shadow-lg" style="z-index:999;">
        <button type="submit" form="createUserForm" 
                class="btn text-white fw-black w-100 d-flex align-items-center justify-content-center gap-2 rounded-3"
                style="background:linear-gradient(135deg, #f59e0b, #d97706); font-size:0.85rem; letter-spacing:0.05em; text-transform:uppercase; padding:14px;">
            <i class="fas fa-user-plus"></i> Register User
        </button>
    </div>

</div>
</div>

<style>
.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1) !important;
    border-color: #f59e0b !important;
    background-color: #fff !important; /* Fixed: reset from red to white */
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /**
     * Reusable filter function
     * @param {string} parentId - ID of the parent select
     * @param {string} childId - ID of the child select
     * @param {string} dataAttr - The data attribute name on child options
     */
    const setupDependentSelect = (parentId, childId, dataAttr) => {
        const parentSelect = document.getElementById(parentId);
        const childSelect = document.getElementById(childId);
        const childOptions = Array.from(childSelect.options);

        parentSelect.addEventListener('change', function () {
            const selectedVal = this.value;
            childSelect.value = ""; // Reset child selection

            childOptions.forEach(option => {
                const linkedId = option.getAttribute(dataAttr);
                
                // Show option if:
                // 1. No parent is selected (standalone mode)
                // 2. It matches the parent
                // 3. It's the "None" option (empty data attribute)
                if (selectedVal === "" || linkedId === selectedVal || linkedId === "") {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
        });
    };

    // Initialize for Faculty -> Department
    setupDependentSelect('faculty_select', 'dept_select', 'data-faculty');

    // Initialize for Office -> Unit
    setupDependentSelect('office_select', 'unit_select', 'data-office');
});
</script>

@endsection