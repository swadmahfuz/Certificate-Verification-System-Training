<?php

use App\Http\Controllers\CertificateController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\SignatoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| These are the routes for the Training Certificate Verification System.
| Ensure all logic is role-protected inside the controller.
|
*/

// --- Public Routes ---
Route::get('/',[CertificateController::class,'search'])->name('certificate.search'); ///load homepage/ certificate verification page with search parameter or no parameter

/// --- Authentication ---
Auth::routes(['register' => false]); ///set to false to disable registration
Route::get('/reset', function () { return view('auth.passwords.email'); } ); ///password reset route
Route::get('/admin', function () { if (Auth::check()){ return redirect()->route('dashboard'); } return view('/login'); } );
Route::get('/login', function () { if (Auth::check()){ return redirect()->route('dashboard'); } return view('/login'); } );
Route::post('/login/addCredentials', [CertificateController::class,'addCredentials'])->name('certificate.login');
Route::get('/logout',[CertificateController::class,'logout']);
///If login page layout changes, remember to update /admin and /login 

/// --- Admin/Authorized Routes ---
Route::get('/dashboard', [CertificateController::class,'getDashboard'])->name('dashboard');
Route::get('/pending-certificates', [CertificateController::class,'getPendingCertificates'])->name('pendingCertificates');
Route::get('/deleted-certificates', [CertificateController::class,'getDeletedCertificates'])->name('deletedCertificates');
Route::get('/add-certificate',[CertificateController::class,'addCertificate']);
Route::post('/add-certificate',[CertificateController::class,'createCertificate'])->name('certificate.create');
Route::get('/view-certificate/{id}',[CertificateController::class,'viewCertificate']);
Route::get('/edit-certificate/{id}',[CertificateController::class,'editCertificate']);
Route::post('/update-certificate',[CertificateController::class,'updateCertificate'])->name('certificate.update');
Route::get('/delete-certificate/{id}',[CertificateController::class,'deleteCertificate']);
Route::get('/admin-search',[CertificateController::class,'adminSearch'])->name('certificate.adminSearch');

    /// --- Review & Approval ---
Route::get('/review-certificate/{id}', [CertificateController::class, 'reviewCertificate'])->name('certificate.review');
Route::get('/approve-certificate/{id}', [CertificateController::class, 'approveCertificate'])->name('certificate.approve');
Route::get('/bulk-review', [CertificateController::class, 'bulkReview'])->name('bulkReview');
Route::get('/bulk-approve', [CertificateController::class, 'bulkApprove'])->name('bulkApprove');

    /// --- PDF Handling ---
Route::post('/upload-pdf/{id}', [CertificateController::class, 'uploadPdf'])->name('certificate.uploadPdf');
Route::get('/download-pdf/{id}', [CertificateController::class, 'downloadPdf'])->name('certificate.downloadPdf');
Route::get('/view-pdf/{id}', [CertificateController::class, 'viewPdf'])->name('certificate.viewPdf');
Route::get('/generate-certificate-pdf/{id}', [CertificateController::class, 'generateCertificatePdf'])->name('certificate.generatePdf');

    /// --- Import/Export ---
Route::get('/imports-exports', [CertificateController::class,'importExportView']);
Route::get('/export', [CertificateController::class, 'export'])->name('export');
Route::post('/import', [CertificateController::class, 'import'])->name('import');

    /// --- Live Search ---
Route::get('/live-search', [CertificateController::class, 'liveSearch'])->name('liveSearch');
Route::get('/live-search-pending', [CertificateController::class, 'liveSearchPending'])->name('liveSearchPending');
Route::get('/live-search-deleted', [CertificateController::class, 'liveSearchDeleted'])->name('liveSearchDeleted');

    /// --- Admin View (optional user list) ---
Route::get('/all-users', [CertificateController::class, 'showAllUsers'])->name('allUsers');

    /// --- Training CVS Trainer Management ---
Route::get('/trainers', [TrainerController::class, 'index'])->name('trainers.index');
Route::get('/trainers/create', [TrainerController::class, 'create'])->name('trainers.create');
Route::post('/trainers', [TrainerController::class, 'store'])->name('trainers.store');
Route::get('/trainers/{id}/edit', [TrainerController::class, 'edit'])->name('trainers.edit');
Route::post('/trainers/{id}/update', [TrainerController::class, 'update'])->name('trainers.update');
Route::post('/trainers/{id}/toggle-status', [TrainerController::class, 'toggleStatus'])->name('trainers.toggleStatus');
Route::get('/trainers/{id}/signature', [TrainerController::class, 'signature'])->name('trainers.signature');

    /// --- Training CVS Signatory Management ---
Route::get('/signatories', [SignatoryController::class, 'index'])->name('signatories.index');
Route::get('/signatories/create', [SignatoryController::class, 'create'])->name('signatories.create');
Route::post('/signatories', [SignatoryController::class, 'store'])->name('signatories.store');
Route::get('/signatories/{id}/edit', [SignatoryController::class, 'edit'])->name('signatories.edit');
Route::post('/signatories/{id}/update', [SignatoryController::class, 'update'])->name('signatories.update');
Route::post('/signatories/{id}/toggle-status', [SignatoryController::class, 'toggleStatus'])->name('signatories.toggleStatus');
Route::get('/signatories/{id}/signature', [SignatoryController::class, 'signature'])->name('signatories.signature');