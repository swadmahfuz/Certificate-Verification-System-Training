<?php

namespace App\Imports;

use App\Models\Certificate;
use Maatwebsite\Excel\Concerns\ToModel;

class CertificateImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Certificate([
            'certificate_number' => $row[0],
            'participant_name' => $row[1],
            'passport_nid' => $row[2],
            'driving_license' => $row[3],
            'company' => $row[4],
            'training_name' => $row[5],
            'trainer' => $row[6],
            'training_date' => $row[7],
            'issue_date' => $row[8],
            'expiry_date' => $row[9],
        ]);
    }
}
