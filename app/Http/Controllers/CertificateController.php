<?php

namespace App\Http\Controllers;
use App\Models\Certificate;
use App\Models\User;
use App\Models\Trainer;
use App\Models\Signatory;
use App\Http\Requests\LoginRequest;
use App\Jobs\ProcessBulkPdfJob;
use App\Jobs\ProcessCertificateImportJob;
use App\Services\BulkPdfService;
use App\Services\CertificateFilterService;
use App\Services\CertificatePdfService;
use App\Services\CertificateSearchService;
use App\Services\DashboardService;
use App\Services\ActivityLogService;
use App\Services\PermissionService;
use App\Exports\CertificateExport;
use App\Imports\CertificateImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Certificate Verification System (CVS) - Training
| TUV Austria Bureau of Inspection & Certification 
| Developed by: Swad Ahmed Mahfuz (Head of Divison - Business Assurance & Training, Bangladesh)
| Contact: swad.mahfuz@gmail.com, +1-725-867-7718, +88 01733 023 008
| Project Start: 12 October 2022
| Latest Stable Release: v5.1.0 -  29 August 2026
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
    public function addCredentials(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $email = $credentials['email'];

        $existing = User::where('email', $email)->first();

        if ($existing && !$existing->isActive()) {
            return redirect('/admin')->with('error', 'Your account has been deactivated. Contact an administrator.');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }

            if ($user->mustChangePassword()) {
                return redirect()->route('account.password.edit')
                    ->with('warning', 'You must set a new password before continuing.');
            }

            return redirect('/dashboard')->with('success', 'Thank You for authorizing. Please proceed.');
        }

        $this->activityLog->record(
            'auth.failed',
            'auth',
            $existing?->id,
            'Failed login attempt for ' . $email . '.',
            ['email' => $email]
        );

        return redirect('/admin')->with('error', 'You entered the wrong credentials');
    }

    
    ////Admin functions
    public function getDashboard(DashboardService $dashboardService)
    {
        return view('dashboard', $dashboardService->data());
    }

    public function indexCertificates(Request $request, CertificateFilterService $filterService)
    {
        $filter = $request->query('filter');
        $query = Certificate::query();
        $filterService->applyFilter($query, $filter);

        $certificates = $query
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate(100)
            ->withQueryString();

        $filterLabels = $filterService->filterLabels($filter);

        return view('certificates.index', compact('certificates', 'filter', 'filterLabels'));
    }


    public function getDeletedCertificates()
    {
        $certificates = Certificate::onlyTrashed()->orderBy('created_at', 'DESC')->orderBy('id', 'DESC')->paginate(100);

        return view('deleted-certificates', compact('certificates'));
    }

    public function getPendingCertificates(Request $request)
    {
        $assignment = $request->query('assignment');
        $query = $this->pendingCertificatesQuery($assignment);

        $certificates = $query
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate(100)
            ->withQueryString();

        return view('pending-certificates', compact('certificates', 'assignment'));
    }

    public function addCertificate()
    {
        $currentYear = date('Y');       ///Pass the current year as YYYY to the view file to populate certificate number
        $currentMonthDay = date('md');  ///Pass the current year as MMDD to the view file to populate certificate number
        $users = User::all();           ///Fetch all users and pass to view to populate "review by" and "approval by" dropdowns
        $trainers = Trainer::where('is_active', true)->orderBy('name', 'asc')->get();  /// Fetch only active trainers for the Trainer dropdown.
        $signatories = Signatory::where('is_active', true)->orderBy('name', 'asc')->get();    /// Fetch only active signatories for the optional Signatory dropdown.

        return view('add-certificate', compact('currentYear', 'currentMonthDay', 'users', 'trainers', 'signatories'));
    }

    public function createCertificate(Request $request)
    {

            $validate = $request->validate([
                'certificate_number' => 'required|unique:training_certificates',
                'certificate_type' => 'required|in:Certificate,Certificate of Achievement,Certificate of Competency,Certificate of Attendance',
                'has_practical' => 'nullable|boolean',
                'is_refresher' => 'nullable|boolean',
                'internal_audit_training' => 'nullable|boolean',
                'online_training' => 'nullable|boolean',
                'participant_name' => 'required',
                'passport_nid' => 'required',
                'training_name' => 'required',
                'location' => 'required',
                'trainer_id' => 'required|integer|exists:training_trainers,id',
                'signatory_id' => 'nullable|integer|exists:training_signatories,id',
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

    public function viewCertificate($id)
    {
        $certificate = Certificate::withTrashed()->find($id);   ///Ensure deleted certificate info can also be viewed by using withTrashed method.

        return view('view-certificate', compact('certificate'));
    }

    public function editCertificate($id)
    {
        if (!app(PermissionService::class)->canMutate()) {
            abort(403, 'You do not have permission to edit certificates.');
        }

        $users = User::all();
        $certificate = Certificate::findOrFail($id);

        ///Show all active trainers. Also retain the certificate's currently selected trainer in the list, even if that trainer has since been deactivated.
        $trainers = Trainer::where('is_active', true)->when($certificate->trainer_id, function ($query) use ($certificate) {
            $query->orWhere('id', $certificate->trainer_id);
        })->orderBy('name', 'asc')->get();

        /// Show all active signatories. Also retain the certificate's currently selected signatory in the list, even if that signatory has since been deactivated.
        $signatories = Signatory::where('is_active', true)->when($certificate->signatory_id, function ($query) use ($certificate) {
            $query->orWhere('id', $certificate->signatory_id);
        })->orderBy('name', 'asc')->get();

        return view('edit-certificate', compact('certificate', 'users', 'trainers', 'signatories'));
    }

    public function updateCertificate(Request $request)
    {

            $validate = $request->validate([
                'certificate_number' => 'required|unique:training_certificates,certificate_number,' . $request->id,
                'certificate_type' => 'required|in:Certificate,Certificate of Achievement,Certificate of Competency,Certificate of Attendance',
                'has_practical' => 'nullable|boolean',
                'is_refresher' => 'nullable|boolean',
                'internal_audit_training' => 'nullable|boolean',
                'online_training' => 'nullable|boolean',
                'participant_name' => 'required',
                'passport_nid' => 'required',
                'training_name' => 'required',
                'location' => 'required',
                'trainer_id' => 'required|integer|exists:training_trainers,id',
                'signatory_id' => 'nullable|integer|exists:training_signatories,id',
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

    // Function to review a certificate
    public function reviewCertificate($id)
    {
        $certificate = Certificate::find($id);

        if (!$certificate) {
            return back()->with('error', 'Certificate not found.');
        }

        if (Auth::id() != $certificate->review_by_id) {
            return back()->with('error', 'Unauthorized: You are not assigned to review this certificate.');
        }

        $certificate->status = 'Pending Approval';      /// Pending Review-> Pending Approval ->Approved
        $certificate->reviewed_at = Carbon::now();
        $certificate->updated_by = Auth::user()->name;
        $certificate->updated_by_id = Auth::id();
        $certificate->updated_at = Carbon::now();
        $certificate->save();

        $this->activityLog->record(
            'certificate.reviewed',
            'certificate',
            $certificate->id,
            'Certificate ' . $certificate->certificate_number . ' was reviewed.'
        );

        return redirect('/view-certificate/' . $certificate->id)
            ->with('success', 'Certificate marked as Reviewed.');
    }

    // Function to approve a certificate
    public function approveCertificate($id)
    {
        $certificate = Certificate::find($id);

        if (!$certificate) {
            return back()->with('error', 'Certificate not found.');
        }

        if (Auth::id() != $certificate->approval_by_id) {
            return back()->with('error', 'Unauthorized: You are not assigned to approve this certificate.');
        }

        if ($certificate->status !== 'Pending Approval') {
            return back()->with('error', 'Certificate must be reviewed before approval.');
        }

        $certificate->status = 'Approved';       /// Pending Review-> Pending Approval ->Approved
        $certificate->approved_at = Carbon::now();
        $certificate->updated_by = Auth::user()->name;
        $certificate->updated_by_id = Auth::id();
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

    public function bulkReview()
    {
        $user = Auth::user();

        $updated = Certificate::where('status', 'Pending Review')
            ->where('review_by_id', $user->id)
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
        $user = Auth::user();

        $updated = Certificate::where('status', 'Pending Approval')
            ->where('approval_by_id', $user->id)
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

    public function bulkReviewSelected(Request $request)
    {
        $ids = $this->validatedSelectedCertificateIds($request);
        $user = Auth::user();

        $eligibleIds = Certificate::whereIn('id', $ids)
            ->assignedForReview($user->id)
            ->pluck('id')
            ->all();

        $updated = Certificate::whereIn('id', $eligibleIds)->update([
            'status' => 'Pending Approval',
            'reviewed_at' => Carbon::now(),
            'updated_by' => $user->name,
            'updated_by_id' => $user->id,
            'updated_at' => Carbon::now(),
        ]);
        $skipped = count($ids) - $updated;

        $this->activityLog->record(
            'certificate.selected_bulk_reviewed',
            'certificate',
            null,
            $updated . ' selected certificate(s) were bulk reviewed.',
            [
                'selected_ids' => $ids,
                'updated_ids' => $eligibleIds,
                'updated_count' => $updated,
                'skipped_count' => $skipped,
            ]
        );

        return back()
            ->with('success', $updated . ' certificate(s) reviewed; ' . $skipped . ' skipped.')
            ->with('bulk_action_completed', true);
    }

    public function bulkApproveSelected(Request $request)
    {
        $ids = $this->validatedSelectedCertificateIds($request);
        $user = Auth::user();

        $eligibleIds = Certificate::whereIn('id', $ids)
            ->assignedForApproval($user->id)
            ->pluck('id')
            ->all();

        $updated = Certificate::whereIn('id', $eligibleIds)->update([
            'status' => 'Approved',
            'approved_at' => Carbon::now(),
            'updated_by' => $user->name,
            'updated_by_id' => $user->id,
            'updated_at' => Carbon::now(),
        ]);
        $skipped = count($ids) - $updated;

        $this->activityLog->record(
            'certificate.selected_bulk_approved',
            'certificate',
            null,
            $updated . ' selected certificate(s) were bulk approved.',
            [
                'selected_ids' => $ids,
                'updated_ids' => $eligibleIds,
                'updated_count' => $updated,
                'skipped_count' => $skipped,
            ]
        );

        return back()
            ->with('success', $updated . ' certificate(s) approved; ' . $skipped . ' skipped.')
            ->with('bulk_action_completed', true);
    }

    public function bulkDeleteSelected(Request $request)
    {
        $ids = $this->validatedSelectedCertificateIds($request);
        $user = Auth::user();

        $deletedIds = DB::transaction(function () use ($ids, $user) {
            $certificates = Certificate::whereIn('id', $ids)
                ->lockForUpdate()
                ->get();
            $deletedIds = [];

            foreach ($certificates as $certificate) {
                $this->softDeleteCertificate($certificate, $user);
                $deletedIds[] = $certificate->id;
            }

            return $deletedIds;
        });
        $skipped = count($ids) - count($deletedIds);

        $this->activityLog->record(
            'certificate.selected_bulk_deleted',
            'certificate',
            null,
            count($deletedIds) . ' selected certificate(s) were deleted.',
            [
                'selected_ids' => $ids,
                'deleted_ids' => $deletedIds,
                'deleted_count' => count($deletedIds),
                'skipped_count' => $skipped,
            ]
        );

        return back()
            ->with('success', count($deletedIds) . ' certificate(s) deleted; ' . $skipped . ' skipped.')
            ->with('bulk_action_completed', true);
    }

    public function deleteCertificate($id)
    {
        $certificate = Certificate::findOrFail($id);
        $this->softDeleteCertificate($certificate, Auth::user());

        $this->activityLog->record(
            'certificate.deleted',
            'certificate',
            $certificate->id,
            'Certificate ' . $certificate->certificate_number . ' was deleted.'
        );

        return back()->with('success', 'Certificate details have been deleted successfully');
    }

    private function validatedSelectedCertificateIds(Request $request): array
    {
        $validated = $request->validate([
            'certificate_ids' => 'required|array|min:1|max:500',
            'certificate_ids.*' => 'required|integer|distinct|exists:training_certificates,id',
        ]);

        return array_map('intval', $validated['certificate_ids']);
    }

    private function softDeleteCertificate(Certificate $certificate, User $user): void
    {
        $certificate->certificate_number .= ' (Deleted)';
        $certificate->status = 'Deleted';
        $certificate->deleted_by = $user->name;
        $certificate->deleted_by_id = $user->id;
        $certificate->reviewed_at = null;
        $certificate->approved_at = null;
        $certificate->updated_by = $user->name;
        $certificate->updated_by_id = $user->id;
        $certificate->updated_at = Carbon::now();
        $certificate->save();
        $certificate->delete();
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
            $user->id == $certificate->created_by_id
        );

        if (!$isAuthorized) {
            return back()->with('error', 'You are not authorized to upload this certificate.');
        }

        $pdfFile = $request->file('certificate_pdf');
        $timestamp = Carbon::now()->format('YmdHi');
        $safeParticipant = preg_replace('/[^A-Za-z0-9\-_. ]+/', '', $certificate->participant_name);
        $fileName = 'TUVAT Training Cert - ' . $safeParticipant . ' ' . $timestamp . '.' . $pdfFile->getClientOriginalExtension();

        // Remove previous private/public copies when replacing.
        $this->deleteCertificatePdfFiles($certificate);

        Storage::putFileAs('certificate-pdfs', $pdfFile, $fileName);

        $certificate->certificate_pdf = $fileName;
        $certificate->pdf_uploaded_by = $user->name;
        $certificate->pdf_uploaded_by_id = $user->id;
        $certificate->pdf_uploaded_at = now();
        $certificate->updated_by = $user->name;
        $certificate->updated_by_id = $user->id;
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
        $filePath = $this->resolveCertificatePdfPath($certificate);

        if (!$filePath) {
            return back()->with('error', 'PDF file not found.');
        }

        return response()->download($filePath, $certificate->certificate_pdf);
    }

    public function viewPdf($id)
    {
        $certificate = Certificate::findOrFail($id);
        $filePath = $this->resolveCertificatePdfPath($certificate);

        if (!$filePath) {
            abort(404, 'PDF not found.');
        }

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $certificate->certificate_pdf . '"'
        ]);
    }

    /**
     * Public PDF view/download for the verification page.
     * Only non-deleted certificates with an uploaded PDF are served.
     */
    public function publicPdf($id)
    {
        $certificate = Certificate::findOrFail($id);

        if ($certificate->status !== 'Approved' || empty($certificate->certificate_pdf)) {
            abort(404, 'PDF not found.');
        }

        $filePath = $this->resolveCertificatePdfPath($certificate);

        if (!$filePath) {
            abort(404, 'PDF not found.');
        }

        $disposition = request()->boolean('download') ? 'attachment' : 'inline';

        return response()->file($filePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $certificate->certificate_pdf . '"'
        ]);
    }

    private function certificatePdfStoragePath(string $fileName): string
    {
        return 'certificate-pdfs/' . $fileName;
    }

    /**
     * Resolve a certificate PDF from private storage, migrating legacy public files when found.
     */
    private function resolveCertificatePdfPath(Certificate $certificate): ?string
    {
        if (empty($certificate->certificate_pdf)) {
            return null;
        }

        $privatePath = $this->certificatePdfStoragePath($certificate->certificate_pdf);

        if (Storage::exists($privatePath)) {
            return Storage::path($privatePath);
        }

        $legacyPath = public_path('Certificate PDFs/' . $certificate->certificate_pdf);
        if (is_file($legacyPath)) {
            Storage::put($privatePath, file_get_contents($legacyPath));
            @unlink($legacyPath);

            return Storage::path($privatePath);
        }

        return null;
    }

    private function deleteCertificatePdfFiles(Certificate $certificate): void
    {
        if (empty($certificate->certificate_pdf)) {
            return;
        }

        $privatePath = $this->certificatePdfStoragePath($certificate->certificate_pdf);
        if (Storage::exists($privatePath)) {
            Storage::delete($privatePath);
        }

        $legacyPath = public_path('Certificate PDFs/' . $certificate->certificate_pdf);
        if (is_file($legacyPath)) {
            @unlink($legacyPath);
        }
    }

    public function generateCertificatePdf($id, CertificatePdfService $certificatePdfService)
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

    public function bulkGenerateCertificatePdfs(
        Request $request,
        BulkPdfService $bulkPdfService
    ) {
        $ids = $this->validatedSelectedCertificateIds($request);

        if (config('queue.default') !== 'sync') {
            ProcessBulkPdfJob::dispatch($ids, Auth::id(), Auth::user()->name);

            return back()->with(
                'success',
                'Bulk PDF generation has been queued. Check the activity log when processing completes.'
            );
        }

        $result = $bulkPdfService->generateZip($ids);

        if ($result === null) {
            return back()->with(
                'error',
                'None of the selected certificates are eligible for PDF generation.'
            );
        }

        $generatedIds = $result['generated_ids'];
        $skipped = $result['skipped'];

        $this->activityLog->record(
            'certificate.selected_bulk_pdf_generated',
            'certificate',
            null,
            count($generatedIds) . ' selected certificate PDF(s) were generated.',
            [
                'selected_ids' => $ids,
                'generated_ids' => $generatedIds,
                'generated_count' => count($generatedIds),
                'skipped_count' => $skipped,
            ]
        );

        $downloadName = 'Training-Certificates-' .
            Carbon::now()->format('Ymd-His') . '.zip';

        return response()
            ->download(Storage::disk('local')->path($result['path']), $downloadName, [
                'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ])
            ->deleteFileAfterSend(true);
    }

    public function liveSearch(Request $request, CertificateSearchService $searchService, CertificateFilterService $filterService)
    {
        $perPage = 100;
        $userInput = (string) ($request->input('userInput') ?? '');
        $filter = $request->query('filter');

        $query = Certificate::query();
        $filterService->applyFilter($query, $filter);

        $result = $searchService->paginate($query, $userInput, $perPage);

        return response()->json(['data' => $result]);
    }

    public function liveSearchDeleted(Request $request, CertificateSearchService $searchService)
    {
        $perPage = 100;
        $userInput = (string) ($request->input('userInput') ?? '');

        $result = $searchService->paginate(Certificate::onlyTrashed(), $userInput, $perPage);

        return response()->json(['data' => $result]);
    }

    public function liveSearchPending(Request $request, CertificateSearchService $searchService)
    {
        $perPage = 100;
        $userInput = (string) ($request->input('userInput') ?? '');
        $assignment = $request->input('assignment');

        $query = $this->pendingCertificatesQuery($assignment);
        $result = $searchService->paginate($query, $userInput, $perPage);

        return response()->json(['data' => $result]);
    }

    private function pendingCertificatesQuery(?string $assignment)
    {
        $userId = Auth::id();

        if ($assignment === 'review' && $userId) {
            return Certificate::assignedForReview($userId);
        }

        if ($assignment === 'approval' && $userId) {
            return Certificate::assignedForApproval($userId);
        }

        if ($assignment === 'mine' && $userId) {
            return Certificate::assignedToUser($userId);
        }

        return Certificate::where(function ($query) {
            $query->whereIn('status', ['Pending Review', 'Pending'])
                ->orWhereIn('status', ['Pending Approval', 'Reviewed']);
        });
    }

    public function importExportView()
    {

            return view('imports-exports');
        
    }

    public function export() 
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        $originalName = $request->file('file')->getClientOriginalName();

        if (config('queue.default') !== 'sync') {
            $storedPath = $request->file('file')->store('imports');

            ProcessCertificateImportJob::dispatch(
                $storedPath,
                $originalName,
                Auth::id(),
                Auth::user()->name
            );

            return back()->with(
                'success',
                'Certificate import has been queued. Check the activity log when processing completes.'
            );
        }

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
                ['file_name' => $originalName]
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

}