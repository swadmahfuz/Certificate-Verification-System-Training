<?php

namespace App\Imports;

use App\Models\Certificate;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Certificate Verification System (CVS) 
| TUV Austria Bureau of Inspection & Certification 
| Developed by: Swad Ahmed Mahfuz (Assistant Manager - Sales & Operations, Bangladesh)
| Contact: swad.mahfuz@gmail.com, +1-725-867-7718, +88 01733 023 008
| Project Start: 12 October 2022
|--------------------------------------------------------------------------
*/

class CertificateImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */      
    public function model(array $row)
    {
        // Check if the user exists in the database and then using the email to get the user ID and name to store in the database
        $createdUser  = User::where('email', $row['created_by_email'])->first();   
        $reviewUser   = User::where('email', $row['review_by_email'])->first();
        $approvalUser = User::where('email', $row['approval_by_email'])->first();
        $loggedInUser = Auth::user();

        return new Certificate([
            'certificate_number' => $row['certificate_number'],
            'participant_name' => $row['participant_name'],
            'passport_nid' => $row['passport_nid'],
            'driving_license' => $row['driving_license'],
            'company' => $row['company'],
            'training_name' => $row['training_name'],
            'location' => $row['location'],
            'trainer' => $row['trainer'],
            'training_date' => $row['training_date'],
            'training_end' => $row['training_end'],
            'issue_date' => $row['issue_date'],
            'expiry_date' => $row['expiry_date'],
            'status' => 'Pending Review', ///Default status "Pending Review" 
            'created_by' => $createdUser ? $createdUser->name : null,
            'created_by_id' => $createdUser ? $createdUser->id : null,
            'created_at' => Carbon::now(),
            'review_by' => $reviewUser ? $reviewUser->name : null,
            'review_by_id' => $reviewUser ? $reviewUser->id : null,
            'approval_by' => $approvalUser ? $approvalUser->name : null,
            'approval_by_id' => $approvalUser ? $approvalUser->id : null,
            'updated_by' => $loggedInUser ? $loggedInUser->name : null,
            'updated_by_id' => $loggedInUser ? $loggedInUser->id : null,
            'updated_at' => Carbon::now(),
        ]);
    }
}
