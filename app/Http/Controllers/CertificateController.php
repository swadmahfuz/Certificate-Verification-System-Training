<?php

namespace App\Http\Controllers;
use App\Models\Certificate;
use App\Models\User;
use App\Models\Trainer;
use App\Models\Signatory;
use App\Services\CertificatePdfService;
use App\Services\DashboardService;
use App\Services\ActivityLogService;
use App\Exports\CertificateExport;
use App\Imports\CertificateImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Certificate Verification System (CVS) - Training
| TUV Austria Bureau of Inspection & Certification 
| Developed by: Swad Ahmed Mahfuz (Head of Divison - Business Assurance & Training, Bangladesh)
| Contact: swad.mahfuz@gmail.com, +1-725-867-7718, +88 01733 023 008
| Project Start: 12 October 2022
| Latest Stable Release: v5.0.0 -  24 July 2026
|--------------------------------------------------------------------------
*/

class CertificateController extends Controller
{
    private $activityLog;

    public function __construct(ActivityLogService $activityLog)
    {
        $this->activityLog = $activityLog;
    }

    ///Unauthenticated user functions
    public function search(Request $request)  ///Public function to search for certificate       
    {
        if ($request->search == null) {
            return view('/verify-certificate');
        }
        $certificate = Certificate::where('certificate_number','=',($request->search))->where('status', 'Approved')->paginate(1);
        return view('verify-certificate',['certificates'=>$certificate]);
    }


    ///Authenticated functions
    public function addCredentials(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $this->activityLog->record(
                'auth.login',
                'auth',
                Auth::id(),
                Auth::user()->name . ' logged in.'
            );
            return redirect('/dashboard')->with('success', 'Thank You for authorizing. Please proceed.');
        }
        else{
            return redirect('/admin')->with('error', 'You entered the wrong credentials');
        }

    }

    public function logout()
    {
        if (Auth::check())
        {
            $this->activityLog->record(
                'auth.logout',
                'auth',
                Auth::id(),
                Auth::user()->name . ' logged out.'
            );
            Auth::logout();
            return redirect('/admin');
        }

        return redirect()->route('certificate.search');
    }
    
    
    ////Admin functions
    public function getDashboard(DashboardService $dashboardService)
    {
        return view('dashboard', $dashboardService->data());
    }

    public function indexCertificates()
    {
        $certificates = Certificate::orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->paginate(100);

        return view('certificates.index', compact('certificates'));
    }


    public function showAllUsers()
    {
        if (Auth::check()) {
            $users = \App\Models\User::withCount([
                'certificatesCreated',
                'certificatesReviewed',
                'certificatesApproved',
            ])->get();

            return view('all-users', compact('users'));
        }
        return redirect()->route('certificate.search');
    }

    public function getDeletedCertificates()
    {
        if (Auth::check())
        {
            $certificates = Certificate::onlyTrashed()->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->paginate(100);
            return view('deleted-certificates',compact('certificates'));
        }

        return redirect()->route('certificate.search');
    }
    
    public function getPendingCertificates()
    {
        if (Auth::check()) {
            $certificates = Certificate::where(function ($query) {
                    $query->whereIn('status', ['Pending Review', 'Pending'])
                        ->orWhereIn('status', ['Pending Approval', 'Reviewed']);
                })
                ->orderBy('created_at', 'DESC')
                ->orderBy('id', 'DESC')
                ->paginate(100);

            return view('pending-certificates', compact('certificates'));
        }

        return redirect()->route('certificate.search');
    }

    public function addCertificate()
    {
        if (Auth::check())
        {
            $currentYear = date('Y');       ///Pass the current year as YYYY to the view file to populate certificate number
            $currentMonthDay = date('md');  ///Pass the current year as MMDD to the view file to populate certificate number
            $users = User::all();           ///Fetch all users and pass to view to populate "review by" and "approval by" dropdowns  
            $trainers = Trainer::where('is_active', true) ->orderBy('name', 'asc') ->get();  /// Fetch only active trainers for the Trainer dropdown.
            $signatories = Signatory::where('is_active', true) ->orderBy('name', 'asc') ->get();    /// Fetch only active signatories for the optional Signatory dropdown.
            return view('add-certificate', compact('currentYear', 'currentMonthDay', 'users', 'trainers', 'signatories'));
        }
        else
        {
            return redirect()->route('certificate.search');
        }
    }

    public function createCertificate(Request $request)
    {
        if (Auth::check())
        {
            $validate = $request->validate([
                'certificate_number' => 'required|unique:certificates_training',
                'certificate_type' => 'required|in:Certificate,Certificate of Achievement,Certificate of Competency,Certificate of Attendance',
                'has_practical' => 'nullable|boolean',
                'is_refresher' => 'nullable|boolean',
                'internal_audit_training' => 'nullable|boolean',
                'online_training' => 'nullable|boolean',
                'participant_name' => 'required',
                'passport_nid' => 'required',
                'training_name' => 'required',
                'location' => 'required',
                'trainer_id' => 'required|integer|exists:certificates_training_trainers,id',
                'signatory_id' => 'nullable|integer|exists:certificates_training_signatories,id',
                'training_date' => 'required',
                'training_end' => 'required',
                'issue_date' => 'required',
                'expiry_date' => 'nullable',
                'review_by' => 'required',
                'approval_by' => 'required',
            ]);

            /// Retrieve the selected active trainer.
            $trainer = Trainer::where('id', $request->trainer_id)
                ->where('is_active', true)
                ->first();

            if (!$trainer) {
                return back()
                    ->withErrors([
                        'trainer_id' => 'The selected trainer is unavailable or inactive.',
                    ])
                    ->withInput();
            }

            /// A trainer signature is required for future certificate generation.
            if (empty($trainer->signature_path)) {
                return back()
                    ->withErrors([
                        'trainer_id' => 'The selected trainer does not have a signature.',
                    ])
                    ->withInput();
            }

            /// Retrieve the selected signatory, if one was selected.
            $signatory = null;

            if ($request->filled('signatory_id')) {
                $signatory = Signatory::where('id', $request->signatory_id)
                    ->where('is_active', true)
                    ->first();

                if (!$signatory) {
                    return back()
                        ->withErrors([
                            'signatory_id' => 'The selected signatory is unavailable or inactive.',
                        ])
                        ->withInput();
                }

                if (empty($signatory->signature_path)) {
                    return back()
                        ->withErrors([
                            'signatory_id' => 'The selected signatory does not have a signature.',
                        ])
                        ->withInput();
                }
            }

            /// Find reviewer and approver user IDs.
            $review_by_user = User::where('name', $request->review_by)->first();

            if ($review_by_user) {
                $review_by_user_id = $review_by_user->id;
            }
            else {
                $review_by_user_id = null;
            }

            $approval_by_user = User::where('name', $request->approval_by)->first();

            if ($approval_by_user) {
                $approval_by_user_id = $approval_by_user->id;
            }
            else {
                $approval_by_user_id = null;
            }

            $certificate = new Certificate();

            $certificate->certificate_number = $request->certificate_number;
            $certificate->certificate_type = $request->certificate_type;

            /// Store training-classification options.
            $certificate->has_practical = $request->boolean('has_practical');
            $certificate->is_refresher = $request->boolean('is_refresher');
            $certificate->internal_audit_training = $request->boolean('internal_audit_training');
            $certificate->online_training = $request->boolean('online_training');

            $certificate->participant_name = $request->participant_name;
            $certificate->passport_nid = $request->passport_nid;
            $certificate->driving_license = $request->driving_license;
            $certificate->company = $request->company;
            $certificate->training_name = $request->training_name;
            $certificate->location = $request->location;

            /// Store trainer details applicable when this certificate is created.
            $certificate->trainer_id = $trainer->id;
            $certificate->trainer = $trainer->name;
            $certificate->trainer_email = $trainer->email;
            $certificate->trainer_designation = $trainer->designation;
            $certificate->trainer_signature_path = $trainer->signature_path;

            /// Store optional signatory details.
            if ($signatory) {
                $certificate->signatory_id = $signatory->id;
                $certificate->signatory_name = $signatory->name;
                $certificate->signatory_email = $signatory->email;
                $certificate->signatory_designation = $signatory->designation;
                $certificate->signatory_department = $signatory->department;
                $certificate->signatory_signature_path = $signatory->signature_path;
            }
            else {
                $certificate->signatory_id = null;
                $certificate->signatory_name = null;
                $certificate->signatory_email = null;
                $certificate->signatory_designation = null;
                $certificate->signatory_department = null;
                $certificate->signatory_signature_path = null;
            }

            $certificate->training_date = $request->training_date;
            $certificate->training_end = $request->training_end;
            $certificate->issue_date = $request->issue_date;
            $certificate->expiry_date = $request->expiry_date;
            $certificate->created_by = Auth::user()->name;
            $certificate->created_by_id = Auth::user()->id;
            $certificate->review_by = $request->review_by;
            $certificate->review_by_id = $review_by_user_id;
            $certificate->approval_by = $request->approval_by;
            $certificate->approval_by_id = $approval_by_user_id;
            $certificate->updated_by = Auth::user()->name;
            $certificate->updated_by_id = Auth::user()->id;
            $certificate->updated_at = Carbon::now();
            $certificate->status = 'Pending Review';
            $certificate->save();

            $this->activityLog->record(
                'certificate.created',
                'certificate',
                $certificate->id,
                'Certificate ' . $certificate->certificate_number . ' was created.',
                ['status' => $certificate->status]
            );

            return redirect('/view-certificate/' . $certificate->id);
        }

        return redirect()->route('certificate.search');
    }

    public function viewCertificate($id)
    {
        if (Auth::check())
        {
            $certificate = Certificate::withTrashed()->find($id);   ///Ensure deleted certificate info can also be viewed by using withTrashed method.
            return view('view-certificate',compact('certificate'));
        }
        return redirect()->route('certificate.search');
    }

    public function editCertificate($id)
    {
        if (Auth::check())
        {
            $users = User::all();
            $certificate = Certificate::findOrFail($id);

            ///Show all active trainers. Also retain the certificate's currently selected trainer in the list, even if that trainer has since been deactivated.
            $trainers = Trainer::where('is_active', true) ->when($certificate->trainer_id, function ($query) use ($certificate) { 
                $query->orWhere('id', $certificate->trainer_id); } ) ->orderBy('name', 'asc') ->get();

            /// Show all active signatories. Also retain the certificate's currently selected signatory in the list, even if that signatory has since been deactivated.
            $signatories = Signatory::where('is_active', true) ->when($certificate->signatory_id, function ($query) use ($certificate) {
                $query->orWhere('id', $certificate->signatory_id);}) ->orderBy('name', 'asc') ->get();

            return view('edit-certificate', compact('certificate', 'users', 'trainers', 'signatories'));
        }
        return redirect()->route('certificate.search');
    }

    public function updateCertificate(Request $request)
    {
        if (Auth::check())
        {
            $validate = $request->validate([
                'certificate_number' => 'required|unique:certificates_training,certificate_number,' . $request->id,
                'certificate_type' => 'required|in:Certificate,Certificate of Achievement,Certificate of Competency,Certificate of Attendance',
                'has_practical' => 'nullable|boolean',
                'is_refresher' => 'nullable|boolean',
                'internal_audit_training' => 'nullable|boolean',
                'online_training' => 'nullable|boolean',
                'participant_name' => 'required',
                'passport_nid' => 'required',
                'training_name' => 'required',
                'location' => 'required',
                'trainer_id' => 'required|integer|exists:certificates_training_trainers,id',
                'signatory_id' => 'nullable|integer|exists:certificates_training_signatories,id',
                'training_date' => 'required',
                'training_end' => 'required',
                'issue_date' => 'required',
                'expiry_date' => 'nullable',
                'review_by' => 'required',
                'approval_by' => 'required',
            ]);

            /// Retrieve the selected trainer.
            $trainer = Trainer::find($request->trainer_id);

            if (!$trainer) {
                return back()
                    ->withErrors([
                        'trainer_id' => 'The selected trainer could not be found.',
                    ])
                    ->withInput();
            }

            /// A trainer signature is required for certificate generation.
            if (empty($trainer->signature_path)) {
                return back()
                    ->withErrors([
                        'trainer_id' => 'The selected trainer does not have a signature.',
                    ])
                    ->withInput();
            }

            /// Retrieve the selected signatory, if one was selected.
            $signatory = null;

            if ($request->filled('signatory_id')) {
                $signatory = Signatory::find($request->signatory_id);

                if (!$signatory) {
                    return back()
                        ->withErrors([
                            'signatory_id' => 'The selected signatory could not be found.',
                        ])
                        ->withInput();
                }

                if (empty($signatory->signature_path)) {
                    return back()
                        ->withErrors([
                            'signatory_id' => 'The selected signatory does not have a signature.',
                        ])
                        ->withInput();
                }
            }

            $review_by_user = User::where('name', $request->review_by)->first();

            if ($review_by_user) {
                $review_by_user_id = $review_by_user->id;
            }
            else {
                $review_by_user_id = null;
            }

            $approval_by_user = User::where('name', $request->approval_by)->first();

            if ($approval_by_user) {
                $approval_by_user_id = $approval_by_user->id;
            }
            else {
                $approval_by_user_id = null;
            }

            $certificate = Certificate::findOrFail($request->id);

            $certificate->certificate_number = $request->certificate_number;
            $certificate->certificate_type = $request->certificate_type;

            /// Store training-classification options.
            $certificate->has_practical = $request->boolean('has_practical');
            $certificate->is_refresher = $request->boolean('is_refresher');
            $certificate->internal_audit_training = $request->boolean('internal_audit_training');
            $certificate->online_training = $request->boolean('online_training');

            $certificate->participant_name = $request->participant_name;
            $certificate->passport_nid = $request->passport_nid;
            $certificate->driving_license = $request->driving_license;
            $certificate->company = $request->company;
            $certificate->training_name = $request->training_name;
            $certificate->location = $request->location;

            /// Store trainer details applicable when this certificate is updated.
            $certificate->trainer_id = $trainer->id;
            $certificate->trainer = $trainer->name;
            $certificate->trainer_email = $trainer->email;
            $certificate->trainer_designation = $trainer->designation;
            $certificate->trainer_signature_path = $trainer->signature_path;

            /// Store optional signatory details.
            if ($signatory) {
                $certificate->signatory_id = $signatory->id;
                $certificate->signatory_name = $signatory->name;
                $certificate->signatory_email = $signatory->email;
                $certificate->signatory_designation = $signatory->designation;
                $certificate->signatory_department = $signatory->department;
                $certificate->signatory_signature_path = $signatory->signature_path;
            }
            else {
                $certificate->signatory_id = null;
                $certificate->signatory_name = null;
                $certificate->signatory_email = null;
                $certificate->signatory_designation = null;
                $certificate->signatory_department = null;
                $certificate->signatory_signature_path = null;
            }

            $certificate->training_date = $request->training_date;
            $certificate->training_end = $request->training_end;
            $certificate->issue_date = $request->issue_date;
            $certificate->expiry_date = $request->expiry_date;
            $certificate->review_by = $request->review_by;
            $certificate->review_by_id = $review_by_user_id;
            $certificate->reviewed_at = null;
            $certificate->approval_by = $request->approval_by;
            $certificate->approval_by_id = $approval_by_user_id;
            $certificate->approved_at = null;
            $certificate->status = 'Pending Review';
            $certificate->updated_by = Auth::user()->name;
            $certificate->updated_by_id = Auth::user()->id;
            $certificate->updated_at = Carbon::now();
            $certificate->save();

            $this->activityLog->record(
                'certificate.updated',
                'certificate',
                $certificate->id,
                'Certificate ' . $certificate->certificate_number . ' was updated and returned for review.',
                ['status' => $certificate->status]
            );

            return redirect('/view-certificate/' . $certificate->id);
        }

        return redirect()->route('certificate.search');
    }

    // Function to review a certificate
    public function reviewCertificate($id)
    {
        if (Auth::check()) {
            $certificate = Certificate::find($id);
            
            if (!$certificate) {
                return back()->with('error', 'Certificate not found.');
            }
            
            if (Auth::user()->id != $certificate->review_by_id) {
                return back()->with('error', 'Unauthorized: You are not assigned to review this certificate.');
            }
            
            $certificate->status = 'Pending Approval';      /// Pending Review-> Pending Approval ->Approved
            $certificate->reviewed_at = Carbon::now();
            $certificate->updated_by = Auth::user()->name;
            $certificate->updated_by_id = Auth::user()->id;
            $certificate->updated_at = Carbon::now();
            $certificate->save();

            $this->activityLog->record(
                'certificate.reviewed',
                'certificate',
                $certificate->id,
                'Certificate ' . $certificate->certificate_number . ' was reviewed.'
            );
            
            return redirect('/view-certificate/' . $certificate->id);
        }
        
        return redirect()->route('certificate.search');
    }

    // Function to approve a certificate
    public function approveCertificate($id)
    {
        if (Auth::check()) {
            $certificate = Certificate::find($id);
            
            if (!$certificate) {
                return back()->with('error', 'Certificate not found.');
            }
            
            if (Auth::user()->id != $certificate->approval_by_id) {
                return back()->with('error', 'Unauthorized: You are not assigned to approve this certificate.');
            }
            
            if ($certificate->status !== 'Pending Approval') {      
                return back()->with('error', 'Certificate must be reviewed before approval.');
            }
            
            $certificate->status = 'Approved';       /// Pending Review-> Pending Approval ->Approved
            $certificate->approved_at = Carbon::now();
            $certificate->updated_by = Auth::user()->name;
            $certificate->updated_by_id = Auth::user()->id;
            $certificate->updated_at = Carbon::now();
            $certificate->save();

            $this->activityLog->record(
                'certificate.approved',
                'certificate',
                $certificate->id,
                'Certificate ' . $certificate->certificate_number . ' was approved.'
            );
            
            return back()->with('success', 'Certificate approved successfully.');
        }

        return redirect()->route('certificate.search');
    }

    public function bulkReview()
    {
        if (!Auth::check())
        {
            return redirect()->route('certificate.search');
        }

        $user = Auth::user();

        $updated = Certificate::where(
            'status',
            'Pending Review'
        )
            ->where(function ($query) use ($user) {
                $query->where(
                    'review_by_id',
                    $user->id
                )
                ->orWhere(
                    'review_by',
                    $user->name
                );
            })
            ->update([
                'status' => 'Pending Approval',
                'reviewed_at' => Carbon::now(),
                'updated_by' => $user->name,
                'updated_by_id' => $user->id,
                'updated_at' => Carbon::now(),
            ]);

        $this->activityLog->record(
            'certificate.bulk_reviewed',
            'certificate',
            null,
            $updated . ' certificate(s) were bulk reviewed.',
            ['count' => $updated]
        );

        return back()->with(
            'success',
            $updated . ' certificate(s) marked as Reviewed.'
        );
    }

    public function bulkApprove()
    {
        if (!Auth::check())
        {
            return redirect()->route('certificate.search');
        }

        $user = Auth::user();

        $updated = Certificate::where(
            'status',
            'Pending Approval'
        )
            ->where(function ($query) use ($user) {
                $query->where(
                    'approval_by_id',
                    $user->id
                )
                ->orWhere(
                    'approval_by',
                    $user->name
                );
            })
            ->update([
                'status' => 'Approved',
                'approved_at' => Carbon::now(),
                'updated_by' => $user->name,
                'updated_by_id' => $user->id,
                'updated_at' => Carbon::now(),
            ]);

        $this->activityLog->record(
            'certificate.bulk_approved',
            'certificate',
            null,
            $updated . ' certificate(s) were bulk approved.',
            ['count' => $updated]
        );

        return back()->with(
            'success',
            $updated . ' certificate(s) marked as Approved.'
        );
    }

    public function deleteCertificate($id)
    {
        if (Auth::check())
        {
            $certificate = Certificate::findOrFail($id);

            // Append "(Deleted)" to the certificate number to avoid duplicates
            $certificate->certificate_number .= " (Deleted)";

            // Update status and deleted_by fields
            $certificate->status = "Deleted";
            $certificate->deleted_by = Auth::user()->name;
            $certificate->deleted_by_id = Auth::user()->id;
            $certificate->reviewed_at = null;
            $certificate->approved_at = null;
            $certificate->updated_by = Auth::user()->name;
            $certificate->updated_by_id = Auth::user()->id;
            $certificate->updated_at = Carbon::now();

            // Save the updates before soft-deleting
            $certificate->save();

            // Soft delete the certificate
            $certificate->delete();

            $this->activityLog->record(
                'certificate.deleted',
                'certificate',
                $certificate->id,
                'Certificate ' . $certificate->certificate_number . ' was deleted.'
            );

            return back()->with('Certificate_Deleted', 'Certificate details have been deleted successfully');
        }

        return redirect()->route('certificate.search');
    }

    public function uploadPdf(Request $request, $id)
    {
        $request->validate([
            'certificate_pdf' => 'required|mimes:pdf|max:20480', // max 20MB
        ]);

        $certificate = Certificate::findOrFail($id);
        
        // Ensure only creator, reviewer, or approver can upload
        $user = Auth::user();
        $isAuthorized = (
            $user->id == $certificate->review_by_id ||
            $user->id == $certificate->approval_by_id ||
            $user->id == $certificate->created_by_id ||
            $user->name == $certificate->review_by ||
            $user->name == $certificate->approval_by ||
            $user->name == $certificate->created_by
        );

        if (!$isAuthorized) {
            return back()->with('error', 'You are not authorized to upload this certificate.');
        }

        $destinationPath = public_path('Certificate PDFs'); // Now inside public
        // Create directory if not exists
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }

        $pdfFile = $request->file('certificate_pdf');
        $timestamp = Carbon::now()->format('YmdHi');
        $fileName = 'TUVAT Training Cert - ' . $certificate->participant_name . ' ' . $timestamp . '.' . $pdfFile->getClientOriginalExtension();

        $pdfFile->move($destinationPath, $fileName);

        $certificate->certificate_pdf = $fileName;
        $certificate->pdf_uploaded_by = $user->name;
        $certificate->pdf_uploaded_by_id = $user->id;
        $certificate->pdf_uploaded_at = now();
        $certificate->updated_by = Auth::user()->name;
        $certificate->updated_by_id = Auth::user()->id;
        $certificate->updated_at = Carbon::now();
        $certificate->save();

        $this->activityLog->record(
            'certificate.pdf_uploaded',
            'certificate',
            $certificate->id,
            'A PDF was uploaded for certificate ' . $certificate->certificate_number . '.'
        );

        return back()->with('success', 'Certificate PDF uploaded successfully.');
    }

    public function downloadPdf($id)
    {
        $certificate = Certificate::findOrFail($id);
        
        $filePath = public_path('Certificate PDFs/' . $certificate->certificate_pdf);

        if (!file_exists($filePath)) {
            return back()->with('error', 'PDF file not found.');
        }

        return response()->download($filePath, $certificate->certificate_pdf);
    }

    public function viewPdf($id)
    {
        $certificate = Certificate::findOrFail($id);
        $filePath = public_path('Certificate PDFs/' . $certificate->certificate_pdf);

        if (!file_exists($filePath)) {
            abort(404, 'PDF not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $certificate->certificate_pdf . '"'
        ]);
    }

    public function generateCertificatePdf($id, CertificatePdfService $certificatePdfService)
    {
        if (Auth::check())
        {
            $certificate = Certificate::findOrFail($id);

            /// Only approved certificates may be generated as PDF.
            if ($certificate->status != 'Approved') {
                return back()->with('pdf_error', 'Only approved certificates can be generated as PDF.');
            }

            /// Generate the PDF in memory without permanently storing it.
            $pdfContent = $certificatePdfService->generateTestPdf($certificate);

            /// Replace characters that are unsafe in downloaded filenames.
            $safeCertificateNumber = preg_replace('/[^A-Za-z0-9\-_.]/', '-', $certificate->certificate_number);

            $filename = 'Training-Certificate-' . $safeCertificateNumber . '.pdf';

            $this->activityLog->record(
                'certificate.pdf_generated',
                'certificate',
                $certificate->id,
                'A PDF was generated for certificate ' . $certificate->certificate_number . '.'
            );

            return response($pdfContent, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Content-Length' => strlen($pdfContent),
                'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ]);
        }

        return redirect()->route('certificate.search');
    }

    ///Live-Search in Dashboard
    public function liveSearch(Request $request)
    {
        if (Auth::check()) {
            $perPage = 100; // Number of certificates per page
            $userInput = $request->input('userInput', '');
    
            if (empty($userInput)) {
                // If the search input is empty, return all certificates ordered by certificate_number descending with pagination
                $result = Certificate::orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->paginate($perPage);
            } else {                
                $result = Certificate::where(function ($query) use ($userInput) {
                    $query->whereRaw('LOWER(certificate_number) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('LOWER(participant_name) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('passport_nid = ?', [$userInput])
                        ->orWhereRaw('driving_license = ?', [$userInput])
                        ->orWhereRaw('LOWER(company) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('LOWER(training_name) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('LOWER(location) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('LOWER(trainer) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('training_date LIKE ?', ['%' . $userInput . '%'])
                        ->orWhereRaw('training_end LIKE ?', ['%' . $userInput . '%'])
                        ->orWhereRaw('issue_date LIKE ?', ['%' . $userInput . '%'])
                        ->orWhereRaw('expiry_date LIKE ?', ['%' . $userInput . '%']);
                })
                ->orderBy('created_at', 'DESC')
                ->orderBy('id', 'DESC')
                ->paginate($perPage);
            }
    
            return response()->json(['data' => $result]);
        } else {
            return redirect()->route('certificate.search');
        }
    }

    public function liveSearchDeleted(Request $request)     // To search within deleted certificates only
    {
        if (Auth::check()) {
            $perPage = 100; // Number of certificates per page
            $userInput = $request->input('userInput', '');
    
            if (empty($userInput)) {
                // If the search input is empty, return all certificates ordered by certificate_number descending with pagination
                $result = Certificate::onlyTrashed()->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->paginate($perPage);
            } else {
                $result = Certificate::onlyTrashed()
                ->where(function ($query) use ($userInput) {
                    $query->whereRaw('LOWER(certificate_number) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('LOWER(participant_name) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('passport_nid = ?', [$userInput])
                        ->orWhereRaw('driving_license = ?', [$userInput])
                        ->orWhereRaw('LOWER(company) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('LOWER(training_name) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('LOWER(location) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('LOWER(trainer) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('training_date LIKE ?', ['%' . $userInput . '%'])
                        ->orWhereRaw('training_end LIKE ?', ['%' . $userInput . '%'])
                        ->orWhereRaw('issue_date LIKE ?', ['%' . $userInput . '%'])
                        ->orWhereRaw('expiry_date LIKE ?', ['%' . $userInput . '%']);
                })
                ->orderBy('created_at', 'DESC')
                ->orderBy('id', 'DESC')
                ->paginate($perPage);
            }
    
            return response()->json(['data' => $result]);
        } else {
            return redirect()->route('certificate.search');
        }
    }

    public function liveSearchPending(Request $request)
    {
        if (Auth::check()) {
            $perPage = 100;
            $userInput = $request->input('userInput', '');

            $query = Certificate::where(function ($query) {
                $query->whereIn('status', ['Pending Review', 'Pending'])
                    ->orWhereIn('status', ['Pending Approval', 'Reviewed']);
            });

            if (!empty($userInput)) {
                $query->where(function ($search) use ($userInput) {
                    $search->whereRaw('LOWER(certificate_number) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('LOWER(participant_name) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('passport_nid = ?', [$userInput])
                        ->orWhereRaw('driving_license = ?', [$userInput])
                        ->orWhereRaw('LOWER(company) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('LOWER(training_name) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('LOWER(location) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('LOWER(trainer) LIKE ?', ['%' . strtolower($userInput) . '%'])
                        ->orWhereRaw('training_date LIKE ?', ['%' . $userInput . '%'])
                        ->orWhereRaw('training_end LIKE ?', ['%' . $userInput . '%'])
                        ->orWhereRaw('issue_date LIKE ?', ['%' . $userInput . '%'])
                        ->orWhereRaw('expiry_date LIKE ?', ['%' . $userInput . '%']);
                });
            }

            $result = $query
                ->orderBy('created_at', 'DESC')
                ->orderBy('id', 'DESC')
                ->paginate($perPage);

            return response()->json(['data' => $result]);
        }

        return redirect()->route('certificate.search');
    }
        

    public function importExportView()
    {
        if (Auth::check())
        {
            return view('imports-exports');
        }
       return redirect()->route('certificate.search');
    }

    public function export() 
    {
        if (Auth::check())
        {
            $today = Carbon::now()->format('d-m-Y');   ///get current date
            $fileName = 'TUV Austria BIC Certificate DB on '.$today.'.xlsx';
            $this->activityLog->record(
                'export.completed',
                'export',
                null,
                'Certificate data was exported.',
                ['file_name' => $fileName]
            );
            return Excel::download(new CertificateExport, $fileName);
        }
        return redirect()->route('certificate.search');
    }

    public function import(Request $request)
    {
        if (Auth::check())
        {
            $request->validate([
                'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
            ]);

            try
            {
                DB::transaction(function () use ($request) {
                    Excel::import(
                        new CertificateImport,
                        $request->file('file')
                    );
                });

                $this->activityLog->record(
                    'import.completed',
                    'import',
                    null,
                    'Certificate data was imported.',
                    ['file_name' => $request->file('file')->getClientOriginalName()]
                );

                return back()->with(
                    'success',
                    'Certificate data imported successfully.'
                );
            }
            catch (\Throwable $e)
            {
                Log::error(
                    'Certificate import failed.',
                    [
                        'user_id' => Auth::id(),
                        'file_name' => $request->file('file')
                            ? $request->file('file')->getClientOriginalName()
                            : null,
                        'error' => $e->getMessage(),
                    ]
                );

                $this->activityLog->record(
                    'import.failed',
                    'import',
                    null,
                    'A certificate import failed.',
                    [
                        'file_name' => $request->file('file')
                            ? $request->file('file')->getClientOriginalName()
                            : null,
                    ]
                );

                $errorMessage = $e->getMessage();

                /*
                * Do not expose database queries or internal SQL errors
                * on the import page.
                */
                if (
                    strpos($errorMessage, 'SQLSTATE') !== false ||
                    strpos($errorMessage, 'Integrity constraint') !== false
                ) {
                    $errorMessage =
                        'The spreadsheet contains duplicate or invalid certificate data.';
                }

                return back()->with(
                    'import_error',
                    'Import failed: ' . $errorMessage
                );
            }
        }

        return redirect()->route('certificate.search');
    }

}