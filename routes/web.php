<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, DashboardController, AssetController, SubmissionController};
use App\Http\Controllers\Admin\{AdminController, UserController,ClassificationController, FacultyController, DepartmentController, OfficeController, UnitController, InstituteController};
use App\Http\Controllers\Staff\{StaffController, GuidelineController};
use App\Http\Controllers\Auditor\AuditorController;

// 1. Public / Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/', fn() => redirect()->route('login'));
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'doLogin'])->name('login.submit');
});

// 2. Authenticated Routes (Shared across roles)
Route::middleware(['auth', 'session.timeout'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('profile')->group(function () {
        Route::get('/password', [AuthController::class, 'showChangePassword'])->name('password.change');
        Route::post('/password', [AuthController::class, 'updatePassword'])->name('password.update');
    });

    // ────────────────────────────────────────────────
    // ADMIN ROUTES (role_id = 1)
    // ────────────────────────────────────────────────
    Route::get('/session-heartbeat', function () {
    return response()->json(['status' => 'session refreshed']);
        })->name('session.heartbeat');

    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'role:1'], function () {

        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        

        // Submissions – full access
        Route::get('/submissions/pending', [SubmissionController::class, 'index'])->name('submissions.pending');
        Route::get('/submissions/{id}', [SubmissionController::class, 'show'])->name('submissions.show');
        Route::resource('submissions', SubmissionController::class)->parameters([
            'submissions' => 'submission_id'
        ])->only(['index', 'show', 'create', 'store', 'edit', 'update']);

        // Assets – full CRUD
        Route::resource('assets', AssetController::class)->parameters(['assets' => 'asset_id']);
        Route::get('/approved-items', [AssetController::class, 'index'])->name('approved_items.index');

        // Reports & Structure Management
        Route::controller(AdminController::class)->group(function () {
            Route::get('College/Management', 'unitsManagementIndex')->name('units-management.index');
            Route::get('reports', 'reportsIndex')->name('reports.index');
            Route::get('reports/export', 'exportReport')->name('reports.export');
            Route::get('structure/export', 'export')->name('structure.export');
            Route::get('/categories', 'unitsManagementIndex')->name('categories.index');
            Route::get('/subcategories','unitsManagementIndex')->name('subcategories.index');
        });

        // Classification Management
        Route::controller(ClassificationController::class)->group(function () {
            Route::get('/registry-management', 'index')->name('classification_categories.index');
            Route::get('/registry-management/sub', 'index')->name('classification_subcategories.index');

            Route::post('/categories', 'storeCategory')->name('categories.store');
            Route::put('/categories/{id}', 'updateCategory')->name('categories.update');
            Route::delete('/categories/{id}', 'destroyCategory')->name('categories.delete');
            
            Route::post('/subcategories', 'storeSubcategory')->name('subcategories.store');
            Route::put('/subcategories/{id}', 'updateSubcategory')->name('subcategories.update');
            Route::delete('/subcategories/{id}', 'destroySubcategory')->name('subcategories.delete');
        });

        // Organizational Resources (CRUD)
        Route::resource('users',      UserController::class)     ->parameters(['users'      => 'user_id']);
        Route::resource('faculties',  FacultyController::class)  ->parameters(['faculties'  => 'faculty_id']);
        Route::resource('depts',      DepartmentController::class)->names('departments')->parameters(['depts' => 'dept_id']);
        Route::resource('offices',    OfficeController::class)   ->parameters(['offices'    => 'office_id']);
        Route::resource('units',      UnitController::class)     ->parameters(['units'      => 'unit_id']);
        Route::resource('institutes', InstituteController::class)->parameters(['institutes' => 'institute_id']);

        // Search Helpers
        // Place this in your admin routes group
        Route::get('/search/staff-leadership', [AdminController::class, 'searchStaff'])
             ->name('search.staff');
    });

    // ────────────────────────────────────────────────
    // STAFF ROUTES (role_id = 2)
    // ────────────────────────────────────────────────
    Route::group(['prefix' => 'staff', 'as' => 'staff.', 'middleware' => 'role:2'], function () {

        Route::get('/dashboard', [StaffController::class, 'index'])->name('dashboard');

        // Submissions – full CRUD for own records
        Route::resource('submissions', SubmissionController::class)->parameters([
            'submissions' => 'submission_id'
        ])->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        // Assets – read-only + exports
        Route::get('assets',          [AssetController::class, 'index'])->name('assets.index');
        Route::get('assets/{asset_id}', [AssetController::class, 'show'])->name('assets.show');

        Route::get('assets/export-pdf', [AssetController::class, 'exportPdf'])->name('assets.export-pdf');
        Route::get('assets/export-csv', [AssetController::class, 'exportCsv'])->name('assets.export-csv');

        // Guidelines
        Route::get('/guidelines',          [GuidelineController::class, 'index'])->name('guidelines.index');
        Route::get('/guidelines/download', [GuidelineController::class, 'downloadManual'])->name('guidelines.download');
    });

    // ────────────────────────────────────────────────
    // AUDITOR ROUTES (role_id = 3)
    // ────────────────────────────────────────────────
  Route::group(['prefix' => 'auditor', 'as' => 'auditor.', 'middleware' => 'role:3'], function () {

    Route::get('/dashboard', [AuditorController::class, 'dashboard'])->name('dashboard');

    // --- SUBMISSIONS (Batches) ---
    Route::get('/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/{id}', [SubmissionController::class, 'show'])->name('submissions.show');

    // --- INDIVIDUAL ITEMS (The "Process" functionality from Registry) ---
    Route::get('/items/{item_id}', [SubmissionController::class, 'showItem'])->name('items.show');

    // --- AUDITOR-SPECIFIC ACTIONS ---
    // Batch actions
    Route::post('/submissions/{submission_id}/store', [AuditorController::class, 'store'])->name('submissions.store');
    
    // Item-specific actions (Approval/Rejection per item)
    Route::post('/items/{item_id}/process', [AuditorController::class, 'processItem'])->name('items.process');
    Route::post('/submissions/{submission_id}/re-evaluate', [AuditorController::class, 'reEvaluate'])->name('submissions.re-evaluate');

    // --- APPROVED / VERIFIED ASSETS ---
    Route::get('/approved-items', [AssetController::class, 'index'])->name('approved_items.index');
    // This route now uses SubmissionController to show the full submission details
    Route::get('/approved-items/{submission_id}', [AssetController::class, 'show'])->name('approved_items.show');
    Route::get('/reports/export', [AssetController::class, 'exportcsv'])->name('reports.export');
    
    // --- CENTRAL REGISTRY ---
    Route::get('/central-registry', [AuditorController::class, 'registryIndex'])->name('registry.index');
    Route::get('/registry/export', [AuditorController::class, 'export'])->name('registry.export');
    
    });
});