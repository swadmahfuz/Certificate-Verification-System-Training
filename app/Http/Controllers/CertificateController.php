<?php

namespace App\Http\Controllers;
use App\Models\Certificate;
use App\Exports\CertificateExport;
use App\Imports\CertificateImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class CertificateController extends Controller
{


    ///Unauthenticated user functions
    public function search(Request $request)
    {
        if ($request->search == null) {
            return view('/verify');
        }
        $certificate = Certificate::where('certificate_number','=',($request->search))->paginate(1);
        return view('verify',['certificates'=>$certificate]);
    }

    ///Authentication functions
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

        return redirect('/admin');;
    }
    
    
    ////Admin functions
    public function getDashboard()
    {
        if (Auth::check())
        {
            $certificates = Certificate::orderBy('certificate_number','DESC')->paginate(100); ///Sorted by certificate number
            return view('dashboard',compact('certificates'));
        }

        return redirect('/admin');
    }

    public function getDeletedCertificates()
    {
        if (Auth::check())
        {
            $certificates = Certificate::onlyTrashed()->orderBy('certificate_number','DESC')->paginate(100);
            return view('dashboard',compact('certificates'));
        }

        return redirect('/admin');
    }
    
    public function addCertificate()
    {
        if (Auth::check())
        {
            return view('add-certificate');
        }
        else
        {
            return redirect('/admin');
        }
    }

    public function createCertificate(Request $request)
    {
        if (Auth::check())
        {
            $validate = $request->validate([
                'certificate_number' => 'required|unique:certificates', ///check if certificate is unique from "Certificates" table
                'participant_name' => 'required',
                'passport_nid' => 'required',
                'training_name' => 'required',
                'location' => 'required',
                'trainer' => 'required',
                'training_date' => 'required',
                'issue_date' => 'required',
                'expiry_date' => 'required',
            ]);
            
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
            $certificate->issue_date = $request->issue_date;
            $certificate->expiry_date = $request->expiry_date;
            $certificate->created_by = Auth::user()->name;
            $certificate->save();
            return redirect('/dashboard');
        }
        return redirect ('/admin');
    }

    public function viewCertificate($id)
    {
        if (Auth::check())
        {
            $certificate = Certificate::withTrashed()->find($id);   ///Ensure deleted certificate info can also be viewed by using withTrashed method.
            return view('view-certificate',compact('certificate'));
        }
        return redirect ('/admin');
    }

    public function editCertificate($id)
    {
        if (Auth::check())
        {
            $certificate = Certificate::find($id);
            return view('edit-certificate',compact('certificate'));
        }
        return redirect ('/admin');
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
                'issue_date' => 'required',
                'expiry_date' => 'required',
            ]);
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
            $certificate->issue_date = $request->issue_date;
            $certificate->expiry_date = $request->expiry_date;
            $certificate->updated_by = Auth::user()->name;
            $certificate->save();
            return redirect('/dashboard');
        }
        return redirect ('/admin');
    }


    public function deleteCertificate($id)
    {
        if (Auth::check())
        {
            $certificate = Certificate::find($id);
            $certificate_number = $certificate->certificate_number . " (Deleted)";
            $deleted_by = Auth::user()->name;
            DB::table('certificates')->where('id', $id)->update(array('certificate_number' => $certificate_number)); ///Concat "(Deleted)" with cert number to prevent duplicate certificate number error. 
            DB::table('certificates')->where('id', $id)->update(array('deleted_by' => $deleted_by));    ///Add user's name to deleted by column.
            Certificate::where('id',$id)->delete();
            return back()->with('Certificate_Deleted','Certificate details has been deleted successfully');
        }
        return redirect ('/admin');
    }

    public function adminSearch(Request $request)
    {
        if (Auth::check())
        {
            $certificates = Certificate::where('certificate_number','=',($request->search))->orWhere('participant_name','LIKE','%'.($request->search).'%')->orWhere('passport_nid','=',($request->search))->orWhere('driving_license','=',($request->search))->orWhere('company','LIKE','%'.($request->search).'%')->orWhere('training_name','LIKE','%'.($request->search).'%')->orWhere('trainer','LIKE','%'.($request->search).'%')->orWhere('training_date','LIKE','%'.($request->search).'%')->paginate(100); ///search using % and LIKE to find words in query
            return view('dashboard',compact('certificates'));
        }
        return redirect ('/admin');
    }

    public function importExportView()
    {
        if (Auth::check())
        {
            return view('imports-exports');
        }
       return redirect('/admin');
    }

    public function export() 
    {
        if (Auth::check())
        {
            $today = Carbon::now()->format('d-m-Y');   ///get current date
            $fileName = 'TUV Austria BIC Certificate DB on '.$today.'.xlsx';
            return Excel::download(new CertificateExport, $fileName);
        }
        return redirect('/admin');
    }

    public function import()
    {
        if (Auth::check())
        {
        Excel::import(new CertificateImport,request()->file('file'));
        return redirect ('/dashboard');
        }
        return redirect ('/admin');
    }

}

    // public function generateQRCode($id)     ///function not used anymore as goQR api is implemented.
    // {
    //     if (Auth::check())
    //     {
    //         $certificate = Certificate::find($id);
    //         return view('qrcode', compact('certificate'));  ///send certificate data to view page
    //     }
    //     return redirect('/admin');
    // }
