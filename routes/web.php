<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\SignatoryController;
use App\Http\Controllers\TrainerController;
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
Route::get('/', [CertificateController::class, 'search'])->name('certificate.search'); /// Homepage / public certificate verification search
Route::get('/certificate-pdf/{id}', [CertificateController::class, 'publicPdf'])->name('certificate.publicPdf'); /// Public PDF for verification page

// --- Authentication ---
Auth::routes(['register' => false]); /// Registration disabled for this application
Route::get('/reset', function () {
    return view('auth.passwords.email');
}); /// Password reset form

Route::get('/admin', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('login');
}); /// Admin entry point; redirects to dashboard when already logged in
Route::get('/login', function () {
    return Auth::check() ? redirect()->route('dashboard') : view('login');
})->name('login'); /// Login page (named for auth middleware redirects)
Route::post('/login/addCredentials', [CertificateController::class, 'addCredentials'])
    ->middleware('guest')
    ->name('certificate.login'); /// Custom login credential check
/// If login page layout changes, remember to update /admin and /login

// --- Admin / Authorized Routes ---
Route::middleware('auth')->group(function () {

    /// --- Dashboard & Certificate Lists ---
    Route::get('/dashboard', [CertificateController::class, 'getDashboard'])->name('dashboard'); /// Analytics overview
    Route::get('/certificates', [CertificateController::class, 'indexCertificates'])->name('certificates.index'); /// All certificates table
    Route::get('/pending-certificates', [CertificateController::class, 'getPendingCertificates'])->name('pendingCertificates'); /// Pending review / approval list
    Route::get('/deleted-certificates', [CertificateController::class, 'getDeletedCertificates'])->name('deletedCertificates'); /// Soft-deleted certificates

    /// --- Certificate CRUD ---
    Route::get('/add-certificate', [CertificateController::class, 'addCertificate'])->name('certificate.createForm'); /// Add certificate form
    Route::post('/add-certificate', [CertificateController::class, 'createCertificate'])->name('certificate.create'); /// Save new certificate
    Route::get('/view-certificate/{id}', [CertificateController::class, 'viewCertificate'])->name('certificate.view'); /// Certificate details
    Route::get('/edit-certificate/{id}', [CertificateController::class, 'editCertificate'])->name('certificate.edit'); /// Edit certificate form
    Route::post('/update-certificate', [CertificateController::class, 'updateCertificate'])->name('certificate.update'); /// Save certificate updates
    Route::delete('/delete-certificate/{id}', [CertificateController::class, 'deleteCertificate'])
        ->name('certificate.delete'); /// Soft-delete certificate
    Route::post('/certificates/bulk-delete', [CertificateController::class, 'bulkDeleteSelected'])
        ->name('certificates.bulkDelete'); /// Soft-delete selected certificates

    /// --- Review & Approval ---
    Route::post('/review-certificate/{id}', [CertificateController::class, 'reviewCertificate'])
        ->name('certificate.review'); /// Mark one certificate as reviewed
    Route::post('/approve-certificate/{id}', [CertificateController::class, 'approveCertificate'])
        ->name('certificate.approve'); /// Mark one certificate as approved
    Route::post('/bulk-review', [CertificateController::class, 'bulkReview'])->name('bulkReview'); /// Review all assigned to current user
    Route::post('/bulk-approve', [CertificateController::class, 'bulkApprove'])->name('bulkApprove'); /// Approve all assigned to current user
    Route::post('/certificates/bulk-review', [CertificateController::class, 'bulkReviewSelected'])
        ->name('certificates.bulkReviewSelected'); /// Review eligible selected certificates
    Route::post('/certificates/bulk-approve', [CertificateController::class, 'bulkApproveSelected'])
        ->name('certificates.bulkApproveSelected'); /// Approve eligible selected certificates

    /// --- PDF Handling ---
    Route::post('/upload-pdf/{id}', [CertificateController::class, 'uploadPdf'])->name('certificate.uploadPdf'); /// Upload certificate PDF
    Route::get('/download-pdf/{id}', [CertificateController::class, 'downloadPdf'])->name('certificate.downloadPdf'); /// Download uploaded PDF
    Route::get('/view-pdf/{id}', [CertificateController::class, 'viewPdf'])->name('certificate.viewPdf'); /// View uploaded PDF inline
    Route::get('/generate-certificate-pdf/{id}', [CertificateController::class, 'generateCertificatePdf'])
        ->name('certificate.generatePdf'); /// Generate and download system PDF
    Route::post('/certificates/bulk-pdf', [CertificateController::class, 'bulkGenerateCertificatePdfs'])
        ->name('certificates.bulkPdf'); /// Generate selected certificate PDFs as a ZIP

    /// --- Import / Export ---
    Route::get('/imports-exports', [CertificateController::class, 'importExportView'])->name('importsExports'); /// Import/export page
    Route::get('/export', [CertificateController::class, 'export'])->name('export'); /// Export Excel workbook
    Route::post('/import', [CertificateController::class, 'import'])->name('import'); /// Import Excel workbook

    /// --- Live Search (AJAX) ---
    Route::get('/live-search', [CertificateController::class, 'liveSearch'])->name('liveSearch'); /// Search all certificates
    Route::get('/live-search-pending', [CertificateController::class, 'liveSearchPending'])->name('liveSearchPending'); /// Search pending certificates
    Route::get('/live-search-deleted', [CertificateController::class, 'liveSearchDeleted'])->name('liveSearchDeleted'); /// Search deleted certificates

    /// --- Users & Activity ---
    Route::get('/all-users', [CertificateController::class, 'showAllUsers'])->name('allUsers'); /// Staff user list
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index'); /// Activity history

    /// --- Trainer Management ---
    Route::get('/trainers', [TrainerController::class, 'index'])->name('trainers.index'); /// Trainer list
    Route::get('/trainers/create', [TrainerController::class, 'create'])->name('trainers.create'); /// Add trainer form
    Route::post('/trainers', [TrainerController::class, 'store'])->name('trainers.store'); /// Save new trainer
    Route::get('/trainers/{id}/edit', [TrainerController::class, 'edit'])->name('trainers.edit'); /// Edit trainer form
    Route::post('/trainers/{id}/update', [TrainerController::class, 'update'])->name('trainers.update'); /// Save trainer updates
    Route::post('/trainers/{id}/toggle-status', [TrainerController::class, 'toggleStatus'])->name('trainers.toggleStatus'); /// Activate / deactivate trainer
    Route::get('/trainers/{id}/signature', [TrainerController::class, 'signature'])->name('trainers.signature'); /// Secure trainer signature image

    /// --- Signatory Management ---
    Route::get('/signatories', [SignatoryController::class, 'index'])->name('signatories.index'); /// Signatory list
    Route::get('/signatories/create', [SignatoryController::class, 'create'])->name('signatories.create'); /// Add signatory form
    Route::post('/signatories', [SignatoryController::class, 'store'])->name('signatories.store'); /// Save new signatory
    Route::get('/signatories/{id}/edit', [SignatoryController::class, 'edit'])->name('signatories.edit'); /// Edit signatory form
    Route::post('/signatories/{id}/update', [SignatoryController::class, 'update'])->name('signatories.update'); /// Save signatory updates
    Route::post('/signatories/{id}/toggle-status', [SignatoryController::class, 'toggleStatus'])->name('signatories.toggleStatus'); /// Activate / deactivate signatory
    Route::get('/signatories/{id}/signature', [SignatoryController::class, 'signature'])->name('signatories.signature'); /// Secure signatory signature image

    /// --- Logout ---
    /// POST logout is provided by Auth::routes(); no GET logout route.
});
