<?php

namespace App\Imports;

use App\Models\Certificate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

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
            'created_by' => $row['created_by'],
        ]);
    }
}
