<?php



use App\Http\Controllers\AccountPasswordController;
use App\Http\Controllers\ActivityLogController;

use App\Http\Controllers\CertificateController;

use App\Http\Controllers\DepartmentController;

use App\Http\Controllers\NoAccessController;

use App\Http\Controllers\SignatoryController;

use App\Http\Controllers\TrainerController;

use App\Http\Controllers\UserManagementController;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| These are the routes for the Training Certificate Verification System.

| Public verification stays open. Admin management routes are protected

| with the auth middleware group below.

|

*/



// --- Public Routes ---

Route::get('/', [CertificateController::class, 'search'])->name('certificate.search');

Route::get('/certificate-pdf/{id}', [CertificateController::class, 'publicPdf'])->name('certificate.publicPdf');



// --- Authentication ---

Auth::routes([
    'register' => config('cvs.registration_enabled', false),
    'verify' => true,
]);

Route::get('/reset', function () {

    return view('auth.passwords.email');

});



Route::get('/admin', function () {

    return Auth::check() ? redirect()->route('dashboard') : view('login');

});

Route::get('/login', function () {

    return Auth::check() ? redirect()->route('dashboard') : view('login');

})->name('login');

Route::post('/login/addCredentials', [CertificateController::class, 'addCredentials'])

    ->middleware('guest')

    ->name('certificate.login');



// --- Authenticated Routes ---

Route::middleware(['auth', 'user.active'])->group(function () {

    Route::get('/account/password', [AccountPasswordController::class, 'edit'])->name('account.password.edit');
    Route::post('/account/password', [AccountPasswordController::class, 'update'])->name('account.password.update');

    Route::middleware(['verified', 'password.changed'])->group(function () {

        Route::get('/no-access', NoAccessController::class)->name('no-access');

        Route::middleware('super.admin')->prefix('admin')->name('admin.')->group(function () {

            Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
            Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
            Route::post('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
            Route::post('/departments/{department}/toggle', [DepartmentController::class, 'toggleStatus'])->name('departments.toggle');

            Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
            Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
            Route::post('/users/{user}/send-password-reset', [UserManagementController::class, 'sendPasswordReset'])->name('users.send-password-reset');
            Route::get('/users/{user}/permissions', [UserManagementController::class, 'editPermissions'])->name('users.permissions.edit');
            Route::post('/users/{user}/permissions', [UserManagementController::class, 'updatePermissions'])->name('users.permissions.update');
        });

        Route::middleware('app.access')->group(function () {

        Route::get('/dashboard', [CertificateController::class, 'getDashboard'])->name('dashboard');

        Route::get('/certificates', [CertificateController::class, 'indexCertificates'])->name('certificates.index');

        Route::get('/pending-certificates', [CertificateController::class, 'getPendingCertificates'])->name('pendingCertificates');

        Route::get('/deleted-certificates', [CertificateController::class, 'getDeletedCertificates'])->name('deletedCertificates');



        Route::get('/add-certificate', [CertificateController::class, 'addCertificate'])->name('certificate.createForm');

        Route::get('/view-certificate/{id}', [CertificateController::class, 'viewCertificate'])->name('certificate.view');

        Route::get('/edit-certificate/{id}', [CertificateController::class, 'editCertificate'])->name('certificate.edit');



        Route::get('/download-pdf/{id}', [CertificateController::class, 'downloadPdf'])->name('certificate.downloadPdf');

        Route::get('/view-pdf/{id}', [CertificateController::class, 'viewPdf'])->name('certificate.viewPdf');

        Route::get('/generate-certificate-pdf/{id}', [CertificateController::class, 'generateCertificatePdf'])

            ->name('certificate.generatePdf');



        Route::get('/imports-exports', [CertificateController::class, 'importExportView'])->name('importsExports');



        Route::get('/live-search', [CertificateController::class, 'liveSearch'])->name('liveSearch');

        Route::get('/live-search-pending', [CertificateController::class, 'liveSearchPending'])->name('liveSearchPending');

        Route::get('/live-search-deleted', [CertificateController::class, 'liveSearchDeleted'])->name('liveSearchDeleted');



        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');



        Route::get('/trainers', [TrainerController::class, 'index'])->name('trainers.index');

        Route::get('/trainers/create', [TrainerController::class, 'create'])->name('trainers.create');

        Route::get('/trainers/{id}/edit', [TrainerController::class, 'edit'])->name('trainers.edit');

        Route::get('/trainers/{id}/signature', [TrainerController::class, 'signature'])->name('trainers.signature');



        Route::get('/signatories', [SignatoryController::class, 'index'])->name('signatories.index');

        Route::get('/signatories/create', [SignatoryController::class, 'create'])->name('signatories.create');

        Route::get('/signatories/{id}/edit', [SignatoryController::class, 'edit'])->name('signatories.edit');

        Route::get('/signatories/{id}/signature', [SignatoryController::class, 'signature'])->name('signatories.signature');



        Route::middleware('app.mutate')->group(function () {

            Route::post('/add-certificate', [CertificateController::class, 'createCertificate'])->name('certificate.create');

            Route::post('/update-certificate', [CertificateController::class, 'updateCertificate'])->name('certificate.update');

            Route::delete('/delete-certificate/{id}', [CertificateController::class, 'deleteCertificate'])

                ->name('certificate.delete');

            Route::post('/certificates/bulk-delete', [CertificateController::class, 'bulkDeleteSelected'])

                ->name('certificates.bulkDelete');



            Route::post('/review-certificate/{id}', [CertificateController::class, 'reviewCertificate'])

                ->name('certificate.review');

            Route::post('/approve-certificate/{id}', [CertificateController::class, 'approveCertificate'])

                ->name('certificate.approve');

            Route::post('/bulk-review', [CertificateController::class, 'bulkReview'])->name('bulkReview');

            Route::post('/bulk-approve', [CertificateController::class, 'bulkApprove'])->name('bulkApprove');

            Route::post('/certificates/bulk-review', [CertificateController::class, 'bulkReviewSelected'])

                ->name('certificates.bulkReviewSelected');

            Route::post('/certificates/bulk-approve', [CertificateController::class, 'bulkApproveSelected'])

                ->name('certificates.bulkApproveSelected');



            Route::post('/upload-pdf/{id}', [CertificateController::class, 'uploadPdf'])->name('certificate.uploadPdf');

            Route::post('/certificates/bulk-pdf', [CertificateController::class, 'bulkGenerateCertificatePdfs'])

                ->name('certificates.bulkPdf');



            Route::get('/export', [CertificateController::class, 'export'])->name('export');

            Route::post('/import', [CertificateController::class, 'import'])->name('import');



            Route::post('/trainers', [TrainerController::class, 'store'])->name('trainers.store');

            Route::post('/trainers/{id}/update', [TrainerController::class, 'update'])->name('trainers.update');

            Route::post('/trainers/{id}/toggle-status', [TrainerController::class, 'toggleStatus'])->name('trainers.toggleStatus');



            Route::post('/signatories', [SignatoryController::class, 'store'])->name('signatories.store');

            Route::post('/signatories/{id}/update', [SignatoryController::class, 'update'])->name('signatories.update');

            Route::post('/signatories/{id}/toggle-status', [SignatoryController::class, 'toggleStatus'])->name('signatories.toggleStatus');

        });

        });

    });

});