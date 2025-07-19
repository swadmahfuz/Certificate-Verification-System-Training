<?php

use App\Http\Controllers\CertificateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Public Routes
Route::get('/',[CertificateController::class,'search'])->name('certificate.search'); ///load homepage/ certificate verification page with search parameter or no parameter

///Authentication Routes
Auth::routes(['register' => false]); ///set to false to disable registration
Route::get('/reset', function () { return view('auth.passwords.email'); } ); ///password reset route

Route::get('/admin', function () { if (Auth::check()){ return redirect()->route('dashboard'); } return view('/login'); } );
Route::get('/login', function () { if (Auth::check()){ return redirect()->route('dashboard'); } return view('/login'); } );
Route::post('/login/addCredentials', [CertificateController::class,'addCredentials'])->name('certificate.login');
Route::get('/logout',[CertificateController::class,'logout']);
///If login page layout changes, remember to update /admin and /login 

//Logged-in user Routes
Route::get('/dashboard', [CertificateController::class,'getDashboard'])->name('dashboard');
Route::get('/pending-certificates', [CertificateController::class,'getPendingCertificates'])->name('pendingCertificates');
Route::get('/deleted-certificates', [CertificateController::class,'getDeletedCertificates'])->name('deletedCertificates');
Route::get('/add-certificate',[CertificateController::class,'addCertificate']);
Route::post('/add-certificate',[CertificateController::class,'createCertificate'])->name('certificate.create');
Route::get('/view-certificate/{id}',[CertificateController::class,'viewCertificate']);
Route::get('/edit-certificate/{id}',[CertificateController::class,'editCertificate']);
Route::post('/update-certificate',[CertificateController::class,'updateCertificate'])->name('certificate.update');
Route::get('/delete-certificate/{id}',[CertificateController::class,'deleteCertificate']);
Route::get('/review-certificate/{id}', [CertificateController::class, 'reviewCertificate'])->name('certificate.review');
Route::get('/approve-certificate/{id}', [CertificateController::class, 'approveCertificate'])->name('certificate.approve');
Route::get('/bulk-review', [CertificateController::class, 'bulkReview'])->name('bulkReview');
Route::get('/bulk-approve', [CertificateController::class, 'bulkApprove'])->name('bulkApprove');
Route::get('/admin-search',[CertificateController::class,'adminSearch'])->name('certificate.adminSearch');
Route::get('/imports-exports', [CertificateController::class,'importExportView']);
Route::get('/export', [CertificateController::class, 'export'])->name('export');
Route::post('/import', [CertificateController::class, 'import'])->name('import');
Route::get('/live-search', [CertificateController::class, 'liveSearch'])->name('liveSearch');
Route::get('/live-search-pending', [CertificateController::class, 'liveSearchPending'])->name('liveSearchPending');
Route::get('/live-search-deleted', [CertificateController::class, 'liveSearchDeleted'])->name('liveSearchDeleted');
Route::get('/all-users', [CertificateController::class, 'showAllUsers'])->name('allUsers');
Route::post('/upload-pdf/{id}', [CertificateController::class, 'uploadPdf'])->name('certificate.uploadPdf');
Route::get('/download-pdf/{id}', [CertificateController::class, 'downloadPdf'])->name('certificate.downloadPdf');
Route::get('/view-pdf/{id}', [CertificateController::class, 'viewPdf'])->name('certificate.viewPdf');
