<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, DashboardController, AssetController, SubmissionController};
use App\Http\Controllers\Admin\{AdminController, UserController, FacultyController, DepartmentController, OfficeController, UnitController, InstituteController};
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Auditor\AuditorController;
use App\Http\Controllers\Staff\GuidelineController;

// 1. Public / Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'doLogin'])->name('login.submit');
});

// 2. Authenticated Routes (All Roles)
Route::middleware(['auth','session.timeout'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('profile')->group(function () {
        Route::get('/password', [AuthController::class, 'showChangePassword'])->name('password.change');
        Route::post('/password', [AuthController::class, 'updatePassword'])->name('password.update');
    });

    // ------------------------------------------------------------------
    // ADMIN ROUTES (role_id = 1)
    // ------------------------------------------------------------------
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'role:1'], function () {
        
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // 1. Submissions - Pointing to the NEW SubmissionController
        Route::post('submissions/{submission_id}/action', [SubmissionController::class, 'handleAction'])->name('submissions.action');
        Route::resource('submissions', SubmissionController::class)->only(['index', 'show', 'create', 'store','edit','update'])->parameters([
            'submissions' => 'submission_id'
        ]);

        // 2. Reports & Management
        Route::controller(AdminController::class)->group(function () {
            Route::get('academic-management', 'unitsManagementIndex')->name('units-management.index');
            Route::get('reports', 'reportsIndex')->name('reports.index');
            Route::get('reports/export', 'exportReport')->name('reports.export');
            Route::get('structure/export', 'export')->name('structure.export');
        });

        // Organizational Structure Resources
        Route::resource('users', UserController::class)->parameters(['users' => 'user_id']);
        Route::resource('faculties', FacultyController::class)->parameters(['faculties' => 'faculty_id']);
        Route::resource('depts', DepartmentController::class)->names('departments')->parameters(['depts' => 'dept_id']);
        Route::resource('offices', OfficeController::class)->parameters(['offices' => 'office_id']);
        Route::resource('units', UnitController::class)->parameters(['units' => 'unit_id']);
        Route::resource('institutes', InstituteController::class)->parameters(['institutes' => 'institute_id']);
        Route::resource('assets', AssetController::class)->parameters(['assets' => 'asset_id']);

        // Search Helpers
        Route::get('/search/heads', [UserController::class, 'searchHeads'])->name('users.searchHeads');
        Route::get('/search/faculties', [FacultyController::class, 'searchDeans'])->name('faculties.searchDeans');
    });

    // ------------------------------------------------------------------
    // STAFF ROUTES (role_id = 2)
    // ------------------------------------------------------------------
    Route::group(['prefix' => 'staff', 'as' => 'staff.', 'middleware' => 'role:2'], function () {
        
        Route::get('/dashboard', [StaffController::class, 'index'])->name('dashboard');

        // Submissions Resource - Shared Controller but scoped to staff view logic
        // Create/Store use SubmissionController to handle the data
        Route::resource('submissions', SubmissionController::class)->only([
            'index', 'create', 'store', 'show', 'edit', 'update', 'destroy'
        ])->parameters(['submissions' => 'submission_id']);

        // Assets View (Read-only for staff)
        // In routes/web.php (Inside your staff group)

        // The List View
        Route::get('assets', [StaffController::class, 'assetsIndex'])->name('assets.index');
        Route::get('assets/export-pdf', [StaffController::class, 'exportPdf'])->name('assets.export-pdf');
        Route::get('assets/export-csv', [StaffController::class, 'exportCsv'])->name('assets.export-csv');
        // The Single Item View
        Route::get('/guidelines', [GuidelineController::class, 'index'])->name('guidelines.index');
        Route::get('/guidelines/download', [GuidelineController::class, 'downloadManual'])->name('guidelines.download');
        Route::get('assets/{asset_id}', [StaffController::class, 'show'])->name('assets.show');
    });

    // ------------------------------------------------------------------
    // AUDITOR ROUTES (role_id = 3)
    // ------------------------------------------------------------------
    // ------------------------------------------------------------------
    // AUDITOR ROUTES (role_id = 3)
    // ------------------------------------------------------------------
    Route::group(['prefix' => 'auditor', 'as' => 'auditor.', 'middleware' => 'role:3'], function () {
        
        // The main dashboard
        Route::get('/dashboard', [AuditorController::class, 'dashboard'])->name('dashboard');

        // Submissions List (Pending/Verified)
        Route::get('/submissions', [AuditorController::class, 'index'])->name('submissions.index');

        // Show and Action
        Route::get('/submissions/{submission_id}', [AuditorController::class, 'show'])->name('submissions.show');
        Route::post('/submissions/{submission_id}/store', [AuditorController::class, 'store'])->name('submissions.store');
        Route::post('/submissions/{submission_id}/edit', [AuditorController::class, 'edit'])->name('submissions.edit');
        
        Route::get('/central_registry', [AuditorController::class, 'registryIndex'])->name('registry.index');
        Route::get('/assets/{id}', [AuditorController::class, 'assetsShow'])->name('assets.show');
        // Registry Oversight
        Route::get('/registry', [AuditorController::class, 'assetsIndex'])->name('assets.index');
        Route::get('/registry/export-pdf', [AuditorController::class, 'exportPdf'])->name('reports.export');

    });
});
