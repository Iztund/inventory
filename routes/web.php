<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, DashboardController, AssetController, SubmissionController};
use App\Http\Controllers\Admin\{AdminController, BulkAssetController, UserController, ClassificationController, FacultyController, DepartmentController, OfficeController, UnitController, InstituteController};
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
    
    // Session Management
    Route::get('/session-heartbeat', function () {
        session(['last_activity_time' => time(), 'heartbeat_tick' => time()]);
        session()->save(); 
        return response()->json([
            'status' => 'session refreshed',
            'expiry' => time() + (config('session.lifetime') * 60)
        ]);
    })->name('session.heartbeat');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('profile')->as('password.')->group(function () {
        Route::get('/password', [AuthController::class, 'showChangePassword'])->name('change');
        Route::post('/password', [AuthController::class, 'updatePassword'])->name('update');
    });

    // ────────────────────────────────────────────────
    // ADMIN ROUTES (role_id = 1)
    // ────────────────────────────────────────────────
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'role:1'], function () {

        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        
        // Submissions
        Route::get('/submissions/pending', [SubmissionController::class, 'index'])->name('submissions.pending');
        Route::resource('submissions', SubmissionController::class)->parameters(['submissions' => 'submission_id']);

        // Assets (Inventory)
        Route::get('/approved-items', [AssetController::class, 'index'])->name('approved_items.index');
        Route::resource('assets', AssetController::class)->parameters(['assets' => 'asset_id']);

        // Bulk Asset Management
        Route::controller(BulkAssetController::class)->prefix('bulk-assets')->as('bulk-assets.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/manual/create', 'createManual')->name('manual.create');
            Route::post('/manual/store', 'storeManual')->name('manual.store');
            Route::get('/csv/create', 'createCsv')->name('csv.create');
            Route::post('/csv/process', 'processCsv')->name('csv.process');
            Route::get('/csv/template', 'downloadTemplate')->name('csv.template');
            Route::get('/{import_id}', 'show')->name('show');
            Route::delete('/{import_id}', 'destroy')->name('destroy');
            Route::post('/{import_id}/generate-tags', 'generateTags')->name('generate-tags');
        });

        // Reports & Management
        Route::controller(AdminController::class)->group(function () {
            Route::get('college/management', 'unitsManagementIndex')->name('units-management.index');
            Route::get('reports', 'reportsIndex')->name('reports.index');
            Route::get('reports/export', 'exportReport')->name('reports.export');
            Route::get('structure/export', 'export')->name('structure.export');
            Route::get('/search/staff-leadership', 'searchStaff')->name('search.staff');
        });

        // Classification (Categories/Subcategories)
        Route::controller(ClassificationController::class)->group(function () {
            Route::get('/registry-management', 'index')->name('classification_categories.index');
            Route::get('/registry-management/subcategories', 'index')->name('classification_subcategories.index');
            Route::post('/categories', 'storeCategory')->name('categories.store');
            Route::put('/categories/{id}', 'updateCategory')->name('categories.update');
            Route::delete('/categories/{id}', 'destroyCategory')->name('categories.delete');
            Route::post('/subcategories', 'storeSubcategory')->name('subcategories.store');
            Route::put('/subcategories/{id}', 'updateSubcategory')->name('subcategories.update');
            Route::delete('/subcategories/{id}', 'destroySubcategory')->name('subcategories.delete');
        });

        // Organizational Hierarchy Resources
        Route::resources([
            'users'      => UserController::class,
            'faculties'  => FacultyController::class,
            'departments'      => DepartmentController::class,
            'offices'    => OfficeController::class,
            'units'      => UnitController::class,
            'institutes' => InstituteController::class,
        ], ['parameters' => [
            'users'      => 'user_id',
            'faculties'  => 'faculty_id',
            'departments'=> 'dept_id',
            'offices'    => 'office_id',
            'units'      => 'unit_id',
            'institutes' => 'institute_id'
        ]]);
    });

    // ────────────────────────────────────────────────
    // STAFF ROUTES (role_id = 2)
    // ────────────────────────────────────────────────
    Route::group(['prefix' => 'staff', 'as' => 'staff.', 'middleware' => 'role:2'], function () {
        Route::get('/dashboard', [StaffController::class, 'index'])->name('dashboard');

        Route::resource('submissions', SubmissionController::class)->parameters(['submissions' => 'submission_id']);

        Route::controller(AssetController::class)->prefix('assets')->as('assets.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/export-pdf', 'exportPdf')->name('export-pdf');
            Route::get('/export-csv', 'exportCsv')->name('export-csv');
            Route::get('/{asset_id}', 'show')->name('show');
        });

        Route::controller(GuidelineController::class)->prefix('guidelines')->as('guidelines.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/download', 'downloadManual')->name('download');
        });
    });

    // ────────────────────────────────────────────────
    // AUDITOR ROUTES (role_id = 3)
    // ────────────────────────────────────────────────
    Route::group(['prefix' => 'auditor', 'as' => 'auditor.', 'middleware' => 'role:3'], function () {
        Route::get('/dashboard', [AuditorController::class, 'dashboard'])->name('dashboard');

        // Submissions & Item Processing
        Route::controller(SubmissionController::class)->group(function () {
            Route::get('/submissions', 'index')->name('submissions.index');
            Route::get('/submissions/{id}', 'show')->name('submissions.show');
            Route::get('/items/{item_id}', 'showItem')->name('items.show');
        });

        Route::controller(AuditorController::class)->group(function () {
            Route::post('/submissions/{submission_id}/store', 'store')->name('submissions.store');
            Route::post('/items/{item_id}/process', 'processItem')->name('items.process');
            Route::post('/submissions/{submission_id}/re-evaluate', 'reEvaluate')->name('submissions.re-evaluate');
            Route::get('/central-registry', 'registryIndex')->name('registry.index');
            Route::get('/registry/export', 'export')->name('registry.export');
            Route::get('/export', 'export')->name('reports.export');
        });

        Route::controller(AssetController::class)->prefix('approved-items')->as('approved_items.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{submission_id}', 'show')->name('show');
            Route::get('/export', 'exportcsv')->name('export');
        });
    });
});