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
            'certificate_id' => $row[0],
            'st_name' => $row[1],
            'st_id' => $row[2],
            'course_code' => $row[3],
            'course_result' => $row[4],
        ]);
    }
}
