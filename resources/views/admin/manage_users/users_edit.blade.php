@extends('layouts.admin')

@section('title', 'Edit User: ' . $user->username)

@section('content')

{{-- Toast for copy confirmation --}}
<div id="copyToast" 
     class="position-fixed top-0 end-0 mt-4 me-4 bg-slate-900 text-white px-4 py-3 rounded-3 shadow-lg d-flex align-items-center gap-3 opacity-0"
     style="z-index:9999; pointer-events:none; transition:opacity 0.3s, transform 0.3s; transform:translateY(-10px);">
    <i class="fas fa-check-circle text-success"></i>
    <span class="fw-bold" style="font-size:0.82rem;">Default password copied!</span>
</div>

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
                <h3 class="fw-black text-slate-900 mb-1" style="font-size:1.5rem; letter-spacing:-0.02em;">Edit User Account</h3>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 bg-transparent p-0" style="font-size:0.8rem;">
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}" class="text-decoration-none text-slate-500">Users</a></li>
                        <li class="breadcrumb-item active text-slate-900 fw-bold">{{ $user->username }}</li>
                    </ol>
                </nav>
            </div>
        </div>

        {{-- Save button (visible on desktop) --}}
        <button type="submit" form="editUserForm" 
                class="btn text-white fw-black d-none d-md-flex align-items-center gap-2 rounded-3 shadow-lg"
                style="background:linear-gradient(135deg, #f59e0b, #d97706); font-size:0.8rem; letter-spacing:0.06em; text-transform:uppercase; padding:12px 28px; transition:transform 0.2s, box-shadow 0.2s;"
                onmouseenter="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 24px rgba(217,119,6,0.4)';"
                onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(217,119,6,0.3)';">
            <i class="fas fa-check-circle"></i> Save Changes
        </button>
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
            <form action="{{ route('admin.users.update', $user->user_id) }}" method="POST" id="editUserForm">
                @csrf
                @method('PUT')

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
                                       style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;"
                                       value="{{ old('full_name', $user->full_name ?? '') }}" 
                                       required
                                       onfocus="this.style.background='#fff'; this.style.borderColor='#0f172a';" 
                                       onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                                @error('full_name')
                                    <div class="text-danger mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">Username</label>
                                <input type="text" name="username" 
                                       class="form-control border-slate-200 bg-slate-50 @error('username') border-danger @enderror" 
                                       style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;"
                                       value="{{ old('username', $user->username) }}" 
                                       required
                                       onfocus="this.style.background='#fff'; this.style.borderColor='#0f172a';" 
                                       onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                                @error('username')
                                    <div class="text-danger mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">Email Address</label>
                                <input type="email" name="email" 
                                       class="form-control border-slate-200 bg-slate-50 @error('email') border-danger @enderror" 
                                       style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;"
                                       value="{{ old('email', $user->email) }}" 
                                       required
                                       onfocus="this.style.background='#fff'; this.style.borderColor='#0f172a';" 
                                       onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                                @error('email')
                                    <div class="text-danger mt-1" style="font-size:0.75rem;">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">Account Status</label>
                                <select name="status" 
                                        class="form-select border-slate-200 bg-slate-50" 
                                        style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;"
                                        onfocus="this.style.background='#fff'; this.style.borderColor='#0f172a';" 
                                        onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                            <select name="role_id" 
                                    class="form-select border-slate-200 bg-slate-50" 
                                    style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;"
                                    onfocus="this.style.background='#fff'; this.style.borderColor='#0f172a';" 
                                    onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}" {{ old('role_id', $user->role_id) == $role->role_id ? 'selected' : '' }}>
                                        {{ $role->role_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Primary Affiliation Cards --}}
                        <div class="rounded-3 p-4 bg-slate-50 border border-slate-200 mb-4">
                            <p class="text-uppercase fw-black text-slate-600 mb-3" style="font-size:0.7rem; letter-spacing:0.12em;">
                                Primary Affiliation <span class="text-danger">*</span> <span class="text-slate-400 fw-normal">(Select One)</span>
                            </p>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label text-slate-500 fw-bold text-uppercase mb-2" style="font-size:0.68rem; letter-spacing:0.08em;">Faculty</label>
                                    <select name="faculty_id" 
                                            class="form-select border-slate-200 bg-white" 
                                            style="padding:0.6rem 0.9rem; border-radius:8px; font-size:0.84rem;">
                                        <option value="">None</option>
                                        @foreach($faculties as $faculty)
                                            <option value="{{ $faculty->faculty_id }}" {{ old('faculty_id', $user->faculty_id) == $faculty->faculty_id ? 'selected' : '' }}>
                                                {{ $faculty->faculty_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-slate-500 fw-bold text-uppercase mb-2" style="font-size:0.68rem; letter-spacing:0.08em;">Institute</label>
                                    <select name="institute_id" 
                                            class="form-select border-slate-200 bg-white" 
                                            style="padding:0.6rem 0.9rem; border-radius:8px; font-size:0.84rem;">
                                        <option value="">None</option>
                                        @foreach($institutes as $institute)
                                            <option value="{{ $institute->institute_id }}" {{ old('institute_id', $user->institute_id) == $institute->institute_id ? 'selected' : '' }}>
                                                {{ $institute->institute_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label text-slate-500 fw-bold text-uppercase mb-2" style="font-size:0.68rem; letter-spacing:0.08em;">Office</label>
                                    <select name="office_id" 
                                            class="form-select border-slate-200 bg-white" 
                                            style="padding:0.6rem 0.9rem; border-radius:8px; font-size:0.84rem;">
                                        <option value="">None</option>
                                        @foreach($offices as $office)
                                            <option value="{{ $office->office_id }}" {{ old('office_id', $user->office_id) == $office->office_id ? 'selected' : '' }}>
                                                {{ $office->office_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Secondary Affiliation --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">Department <span class="text-slate-400 fw-normal">(Optional)</span></label>
                                <select name="dept_id" 
                                        class="form-select border-slate-200 bg-slate-50" 
                                        style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;"
                                        onfocus="this.style.background='#fff'; this.style.borderColor='#0f172a';" 
                                        onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                                    <option value="">None</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->dept_id }}" {{ old('dept_id', $user->dept_id) == $department->dept_id ? 'selected' : '' }}>
                                            {{ $department->dept_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">Unit <span class="text-slate-400 fw-normal">(Optional)</span></label>
                                <select name="unit_id" 
                                        class="form-select border-slate-200 bg-slate-50" 
                                        style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;"
                                        onfocus="this.style.background='#fff'; this.style.borderColor='#0f172a';" 
                                        onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                                    <option value="">None</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->unit_id }}" {{ old('unit_id', $user->unit_id) == $unit->unit_id ? 'selected' : '' }}>
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
                                <input type="text" name="phone" 
                                       class="form-control border-slate-200 bg-slate-50" 
                                       style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;"
                                       value="{{ old('phone', $user->profile->phone ?? '') }}"
                                       onfocus="this.style.background='#fff'; this.style.borderColor='#0f172a';" 
                                       onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">
                            </div>

                            <div class="col-12">
                                <label class="form-label text-slate-700 fw-bold mb-2" style="font-size:0.82rem;">Address</label>
                                <textarea name="address" 
                                          class="form-control border-slate-200 bg-slate-50" 
                                          style="padding:0.75rem 1rem; border-radius:10px; font-size:0.88rem;" 
                                          rows="2"
                                          onfocus="this.style.background='#fff'; this.style.borderColor='#0f172a';" 
                                          onblur="this.style.background='#f8fafc'; this.style.borderColor='#e2e8f0';">{{ old('address', $user->profile->address ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- RIGHT COLUMN: Security & Metadata --}}
        <div class="col-lg-4">
            {{-- Danger Zone: Password Reset --}}
            <div class="rounded-4 bg-white border border-danger border-opacity-25 overflow-hidden mb-4 shadow-sm">
                <div class="p-3 bg-danger bg-opacity-10 border-bottom border-danger border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-shield-halved text-danger" style="font-size:0.95rem;"></i>
                        <h6 class="fw-black text-danger-emphasis mb-0 text-uppercase" style="font-size:0.72rem; letter-spacing:0.12em;">Danger Zone</h6>
                    </div>
                </div>
                <div class="p-4">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="reset_password" name="reset_password" value="1" form="editUserForm" 
                               style="width:2.5rem; height:1.25rem; cursor:pointer;">
                        <label class="form-check-label fw-bold text-slate-900 ms-2" for="reset_password" style="font-size:0.85rem; cursor:pointer;">
                            Reset to Default Password
                        </label>
                    </div>
                    <div class="rounded-3 p-3 bg-slate-50 border border-slate-200">
                        <p class="text-slate-600 mb-2" style="font-size:0.75rem;">Default password format:</p>
                        <div class="d-flex align-items-center gap-2">
                            <code id="defaultPwText" class="flex-grow-1 px-3 py-2 bg-white border border-slate-200 rounded-2 text-slate-900 fw-bold" style="font-size:0.82rem;">{{ $user->username }}@ {{ date('Y') }}</code>
                            <button type="button" class="btn btn-sm btn-white border border-slate-200 rounded-2" onclick="copyDefaultPw()" style="padding:0.5rem 0.75rem;">
                                <i class="fas fa-copy text-slate-600" style="font-size:0.75rem;"></i>
                            </button>
                        </div>
                    </div>
                    <p class="text-danger-emphasis mt-3 mb-0" style="font-size:0.73rem; line-height:1.5;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        User will be forced to change password on next login.
                    </p>
                </div>
            </div>

            {{-- Metadata Card --}}
            <div class="rounded-4 bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-3 bg-slate-50 border-bottom border-slate-200">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-clock text-slate-600" style="font-size:0.9rem;"></i>
                        <h6 class="fw-black text-slate-900 mb-0 text-uppercase" style="font-size:0.72rem; letter-spacing:0.12em;">Account Metadata</h6>
                    </div>
                </div>
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-slate-100">
                        <span class="text-slate-600 fw-semibold" style="font-size:0.78rem;">User ID</span>
                        <span class="badge bg-slate-100 text-slate-900 fw-bold" style="font-size:0.75rem; padding:0.4rem 0.8rem;">{{ str_pad($user->user_id, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-slate-100">
                        <span class="text-slate-600 fw-semibold" style="font-size:0.78rem;">Created</span>
                        <span class="text-slate-900 fw-bold" style="font-size:0.8rem;">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-slate-600 fw-semibold" style="font-size:0.78rem;">Last Updated</span>
                        <span class="text-slate-900 fw-bold" style="font-size:0.8rem;">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Sticky Mobile Save Button --}}
    <div class="d-md-none position-fixed bottom-0 start-0 end-0 p-3 bg-white border-top border-slate-200 shadow-lg" style="z-index:999;">
        <button type="submit" form="editUserForm" 
                class="btn text-white fw-black w-100 d-flex align-items-center justify-content-center gap-2 rounded-3"
                style="background:linear-gradient(135deg, #f59e0b, #d97706); font-size:0.85rem; letter-spacing:0.05em; text-transform:uppercase; padding:14px;">
            <i class="fas fa-check-circle"></i> Save Changes
        </button>
    </div>

</div>
</div>

<style>
/* Focus states for inputs */
.form-control:focus, .form-select:focus {
    box-shadow: 0 0 0 3px rgba(15,23,42,0.08) !important;
}

/* Toast show state */
.toast-show {
    opacity: 1 !important;
    transform: translateY(0) !important;
    pointer-events: auto !important;
}

/* Switch styling */
.form-check-input:checked {
    background-color: #dc2626;
    border-color: #dc2626;
}
</style>

<script>
function copyDefaultPw() {
    const text = document.getElementById('defaultPwText').innerText.trim();
    navigator.clipboard.writeText(text).then(() => {
        const toast = document.getElementById('copyToast');
        toast.classList.add('toast-show');
        setTimeout(() => toast.classList.remove('toast-show'), 2000);
    });
}
</script>

@endsection