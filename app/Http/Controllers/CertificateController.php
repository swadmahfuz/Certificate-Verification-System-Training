<?php

namespace App\Http\Controllers;
use App\Models\Certificate;
use App\Models\User;
use App\Models\Trainer;
use App\Models\Signatory;
use App\Services\CertificatePdfService;
use App\Exports\CertificateExport;
use App\Imports\CertificateImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

/*
|--------------------------------------------------------------------------
| Certificate Verification System (CVS) - Training
| TUV Austria Bureau of Inspection & Certification 
| Developed by: Swad Ahmed Mahfuz (Head of Divison - Business Assurance & Training, Bangladesh)
| Contact: swad.mahfuz@gmail.com, +1-725-867-7718, +88 01733 023 008
| Project Start: 12 October 2022
| Latest Stable Release: v4.1.0 -  14 July 2026
|--------------------------------------------------------------------------
*/

class CertificateController extends Controller
{


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
            // Authentication passed...
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
            Auth::logout();
            return redirect('/admin');
        }

        return redirect()->route('certificate.search');
    }
    
    
    ////Admin functions
    public function getDashboard()
    {
        if (Auth::check())
        {
            $certificates = Certificate::orderBy('certificate_number','DESC')->paginate(100); ///Sorted by certificate number
            return view('dashboard',compact('certificates'));
        }

        return redirect()->route('certificate.search');
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
            $certificates = Certificate::onlyTrashed()->orderBy('certificate_number','DESC')->paginate(100);
            return view('deleted-certificates',compact('certificates'));
        }

        return redirect()->route('certificate.search');
    }
    
    public function getPendingCertificates()
    {
        if (Auth::check()) {
            $userId = Auth::user()->id;
            $userName = Auth::user()->name;
    
            $query = Certificate::where(function ($query) use ($userId, $userName) {
                $query->where(function ($q) use ($userId, $userName) {
                    $q->where('status', 'Pending Review')
                      ->where(function ($subQuery) use ($userId, $userName) {
                          $subQuery->where('review_by_id', $userId)
                                   ->orWhere('review_by', $userName);
                      });
                })
                ->orWhere(function ($q) use ($userId, $userName) {
                    $q->where('status', 'Pending Approval')
                      ->where(function ($subQuery) use ($userId, $userName) {
                          $subQuery->where('approval_by_id', $userId)
                                   ->orWhere('approval_by', $userName);
                      });
                });
            })
            ->whereNotIn('status', ['Approved', 'approved', ' APPROVED']) // Explicitly exclude Approved
            ->orderBy('certificate_number', 'DESC');
    
            // Debugging: Check generated SQL
            // dd($query->toSql(), $query->getBindings());
    
            // Execute query
            $certificates = $query->paginate(100);
    
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
                'certificate_number' => 'required|unique:certificates_training', ///check if certificate is unique from "Certificates" table
                'certificate_type' => 'required|in:Certificate,Certificate of Achievement,Certificate of Competency,Certificate of Attendance',
                'participant_name' => 'required',
                'passport_nid' => 'required',
                'training_name' => 'required',
                'location' => 'required',
                'trainer_id' => 'required|integer|exists:certificates_training_trainers,id',
                'signatory_id' => 'nullable|integer|exists:certificates_training_signatories,id',
                'training_date' => 'required',
                'training_end' => 'required',
                'issue_date' => 'required',
                'expiry_date' => 'nullable',    /// To include certificates that do not have expiry date.
                'review_by' => 'required',
                'approval_by' => 'required',
            ]);

            /// Retrieve the selected active trainer.
            $trainer = Trainer::where('id', $request->trainer_id) ->where('is_active', true) ->first();
            if (!$trainer) {
                return back()->withErrors(['trainer_id' => 'The selected trainer is unavailable or inactive.',]) ->withInput();
            }

            /// A trainer signature is required for future certificate generation.
            if (empty($trainer->signature_path)) {
                return back()->withErrors(['trainer_id' => 'The selected trainer does not have a signature.',]) ->withInput();
            }

            /// Retrieve the selected signatory, if one was selected.
            $signatory = null;

            if ($request->filled('signatory_id')) {
                $signatory = Signatory::where('id', $request->signatory_id) ->where('is_active', true) ->first();

                if (!$signatory) {
                    return back()->withErrors(['signatory_id' => 'The selected signatory is unavailable or inactive.',]) ->withInput();
                }

                if (empty($signatory->signature_path)) {
                    return back()->withErrors(['signatory_id' => 'The selected signatory does not have a signature.',]) ->withInput();
                }
            }

            ///The following code block is to find out user IDs of reviewer and approver in case the name of user changes and there are certificates pending review or approval.

            $review_by_user = User::where('name', $request->review_by)->first();
            if ($review_by_user) {
                $review_by_user_id = $review_by_user->id; // Store the found user ID in a variable
            } else {
                $review_by_user_id = null; // Handle cases where no matching user is found
            }

            $approval_by_user = User::where('name', $request->approval_by)->first();
            if ($approval_by_user) {
                $approval_by_user_id = $approval_by_user->id; // Store the found user ID in a variable
            } else {
                $approval_by_user_id = null; // Handle cases where no matching user is found
            }
            
            $certificate = new Certificate();
            $certificate->certificate_number = $request->certificate_number;
            $certificate->certificate_type = $request->certificate_type;
            $certificate->participant_name = $request->participant_name;
            $certificate->passport_nid = $request->passport_nid;
            $certificate->driving_license = $request->driving_license;
            $certificate->company = $request->company;
            $certificate->training_name = $request->training_name;
            $certificate->location = $request->location;
            /// Store the selected trainer and the trainer details applicable when this certificate is created.
            $certificate->trainer_id = $trainer->id;
            $certificate->trainer = $trainer->name;
            $certificate->trainer_email = $trainer->email;
            $certificate->trainer_designation = $trainer->designation;
            $certificate->trainer_signature_path = $trainer->signature_path;
            if ($signatory) {     /// Store the optional signatory details applicable when this certificate is created.
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
            $certificate->status = "Pending Review";       ///Certificate status flow: Pending Review-> Pending Approval ->Approved
            $certificate->save();
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
                'participant_name' => 'required',
                'passport_nid' => 'required',
                'training_name' => 'required',
                'location' => 'required',
                'trainer_id' => 'required|integer|exists:certificates_training_trainers,id',
                'signatory_id' => 'nullable|integer|exists:certificates_training_signatories,id',
                'training_date' => 'required',
                'training_end' => 'required',
                'issue_date' => 'required',
                'expiry_date' => 'nullable',        /// To include certificates that do not have expiry date.
                'review_by' => 'required',
                'approval_by' => 'required',
            ]);

            
            $trainer = Trainer::find($request->trainer_id);     /// Retrieve the selected trainer.
            if (!$trainer) {
                return back()->withErrors(['trainer_id' => 'The selected trainer could not be found.'])->withInput();
            }

            $signatory = null;     /// Retrieve the selected signatory, if one was selected.
            if ($request->filled('signatory_id')) {
                $signatory = Signatory::find($request->signatory_id);

                if (!$signatory) {
                    return back()->withErrors(['signatory_id' => 'The selected signatory could not be found.'])->withInput();
                }

                if (empty($signatory->signature_path)) {
                    return back()->withErrors(['signatory_id' => 'The selected signatory does not have a signature.'])->withInput();
                }
            }

            $review_by_user = User::where('name', $request->review_by)->first();
            if ($review_by_user) {
                $review_by_user_id = $review_by_user->id; // Store the found user ID in a variable
            } else {
                $review_by_user_id = null; // Handle cases where no matching user is found
            }

            $approval_by_user = User::where('name', $request->approval_by)->first();
            if ($approval_by_user) {
                $approval_by_user_id = $approval_by_user->id; // Store the found user ID in a variable
            } else {
                $approval_by_user_id = null; // Handle cases where no matching user is found
            }

            $certificate = Certificate::findOrFail($request->id);
            $certificate->certificate_number = $request->certificate_number;
            $certificate->certificate_type = $request->certificate_type;
            $certificate->participant_name = $request->participant_name;
            $certificate->passport_nid = $request->passport_nid;
            $certificate->driving_license = $request->driving_license;
            $certificate->company = $request->company;
            $certificate->training_name = $request->training_name;
            $certificate->location = $request->location;
            $certificate->trainer_id = $trainer->id;    /// Store the selected trainer and the trainer details applicable when this certificate is updated.
            $certificate->trainer = $trainer->name;
            $certificate->trainer_email = $trainer->email;
            $certificate->trainer_designation = $trainer->designation;
            $certificate->trainer_signature_path = $trainer->signature_path;
            if ($signatory) {   /// Store the optional signatory details applicable when this certificate is updated.
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
            $certificate->status = "Pending Review";       ///Status changed back to pending if any update is made and will again require review and approval 
            $certificate->updated_by = Auth::user()->name;
            $certificate->updated_by_id = Auth::user()->id;
            $certificate->updated_at = Carbon::now();
            $certificate->save();
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
            
            return back()->with('success', 'Certificate approved successfully.');
        }

        return redirect()->route('certificate.search');
    }

    public function bulkReview()
    {
        $user = Auth::user();
    
        // Mark all 'Pending Review' certificates assigned to the logged-in reviewer
        $updated = DB::table('certificates_training')
            ->where('status', 'Pending Review')
            ->where(function ($query) use ($user) {
                $query->where('review_by_id', $user->id)
                      ->orWhere('review_by', $user->name);
            })
            ->update([
                'status' => 'Pending Approval',
                'updated_by' => $user->name,
                'updated_by_id' => $user->id,
                'updated_at' => Carbon::now(),
                'reviewed_at' => Carbon::now(),
            ]);
    
        return redirect()->back()->with('success', "$updated certificate(s) marked as Reviewed.");
    }
    
    public function bulkApprove()
    {
        $user = Auth::user();
    
        // Mark all 'Pending Approval' certificates assigned to the logged-in approver
        $updated = DB::table('certificates_training')
            ->where('status', 'Pending Approval')
            ->where(function ($query) use ($user) {
                $query->where('approval_by_id', $user->id)
                      ->orWhere('approval_by', $user->name);
            })
            ->update([
                'status' => 'Approved',
                'updated_by' => $user->name,
                'updated_by_id' => $user->id,
                'updated_at' => Carbon::now(),
                'approved_at' => Carbon::now(),
            ]);
    
        return redirect()->back()->with('success', "$updated certificate(s) marked as Approved.");
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
                $result = Certificate::orderBy('certificate_number', 'desc')->paginate($perPage);
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
                ->orderBy('certificate_number', 'desc')
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
                $result = Certificate::onlyTrashed()->orderBy('certificate_number', 'desc')->paginate($perPage);
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
                ->orderBy('certificate_number', 'desc')
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
            $perPage = 100; // Number of certificates per page
            $userInput = $request->input('userInput', '');
            $userId = Auth::user()->id;
            $userName = Auth::user()->name;
    
            if (empty($userInput)) {
                // If the search input is empty, return only pending review and approval certificates assigned to the logged-in user
                $result = Certificate::where(function ($query) use ($userId, $userName) {
                    $query->where(function ($q) use ($userId, $userName) {
                        $q->where('status', 'Pending Review')
                          ->where(function ($subQuery) use ($userId, $userName) {
                              $subQuery->where('review_by_id', $userId)
                                       ->orWhere('review_by', $userName);
                          });
                    })
                    ->orWhere(function ($q) use ($userId, $userName) {
                        $q->where('status', 'Pending Approval')
                          ->where(function ($subQuery) use ($userId, $userName) {
                              $subQuery->where('approval_by_id', $userId)
                                       ->orWhere('approval_by', $userName);
                          });
                    });
                })
                ->orderBy('certificate_number', 'desc')
                ->paginate($perPage);
            } else {
                // Search within pending review and approval certificates assigned to the logged-in user
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
                ->where(function ($query) use ($userId, $userName) {
                    $query->where(function ($q) use ($userId, $userName) {
                        $q->where('status', 'Pending Review')
                          ->where(function ($subQuery) use ($userId, $userName) {
                              $subQuery->where('review_by_id', $userId)
                                       ->orWhere('review_by', $userName);
                          });
                    })
                    ->orWhere(function ($q) use ($userId, $userName) {
                        $q->where('status', 'Pending Approval')
                          ->where(function ($subQuery) use ($userId, $userName) {
                              $subQuery->where('approval_by_id', $userId)
                                       ->orWhere('approval_by', $userName);
                          });
                    });
                })
                ->orderBy('certificate_number', 'desc')
                ->paginate($perPage);
            }
    
            return response()->json(['data' => $result]);
        } else {
            return redirect()->route('certificate.search');
        }
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

            try {
                Excel::import(new CertificateImport, $request->file('file'));

                return back()->with('success', 'Certificate data imported successfully.');
            } catch (\Throwable $e) {
                return back()->with('import_error', 'Import failed: ' . $e->getMessage());
            }
        }
        return redirect()->route('certificate.search');
    }

}