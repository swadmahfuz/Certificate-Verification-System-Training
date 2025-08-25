<?php

namespace App\Http\Controllers;
use App\Models\Certificate;
use App\Models\User;
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
| Latest Stable Release: v3.2.2 -  16 August 2025
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
            $users = User::all(); ///Fetch all users and pass to view to populate "review by" and "approval by" dropdowns  
            return view('add-certificate', compact('currentYear', 'currentMonthDay', 'users'));
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
                'participant_name' => 'required',
                'passport_nid' => 'required',
                'training_name' => 'required',
                'location' => 'required',
                'trainer' => 'required',
                'training_date' => 'required',
                'training_end' => 'required',
                'issue_date' => 'required',
                'expiry_date' => 'nullable',    /// To include certificates that do not have expiry date.
                'review_by' => 'required',
                'approval_by' => 'required',
            ]);

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
            
            $certificate = new certificate();
            $certificate->certificate_number = $request->certificate_number;
            $certificate->participant_name = $request->participant_name;
            $certificate->passport_nid = $request->passport_nid;
            $certificate->driving_license = $request->driving_license;
            $certificate->company = $request->company;
            $certificate->training_name = $request->training_name;
            $certificate->location = $request->location;
            $certificate->trainer = $request->trainer;
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
            $users = User::all(); ///Fetch all users and pass to view to populate "review by" and "approval by" dropdowns 
            $certificate = Certificate::find($id);
            return view('edit-certificate',compact('certificate', 'users'));
        }
        return redirect()->route('certificate.search');
    }

    public function updateCertificate(Request $request)
    {
        if (Auth::check())
        {
            $validate = $request->validate([
                'certificate_number' => 'required',
                'participant_name' => 'required',
                'passport_nid' => 'required',
                'training_name' => 'required',
                'location' => 'required',
                'trainer' => 'required',
                'training_date' => 'required',
                'training_end' => 'required',
                'issue_date' => 'required',
                'expiry_date' => 'nullable',        /// To include certificates that do not have expiry date.
                'review_by' => 'required',
                'approval_by' => 'required',
            ]);

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

            $certificate = Certificate::find($request->id);
            $certificate->certificate_number = $request->certificate_number;
            $certificate->participant_name = $request->participant_name;
            $certificate->passport_nid = $request->passport_nid;
            $certificate->driving_license = $request->driving_license;
            $certificate->company = $request->company;
            $certificate->training_name = $request->training_name;
            $certificate->location = $request->location;
            $certificate->trainer = $request->trainer;
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

    public function import()
    {
        if (Auth::check())
        {
        Excel::import(new CertificateImport,request()->file('file'));
        return redirect ('/dashboard');
        }
        return redirect()->route('certificate.search');
    }

}