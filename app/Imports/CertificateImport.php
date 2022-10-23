<?php

namespace App\Imports;

use App\Models\Certificate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

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
            'trainer' => $row['trainer'],
            'training_date' => $row['training_date'],
            'issue_date' => $row['issue_date'],
            'expiry_date' => $row['expirty_date'],
            'created_by' => $row['created_by'],
        ]);
    }
}
