<?php

namespace App\Exports;

use App\Models\Certificate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CertificateExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Certificate::all();
    }
    public function headings(): array
    {
        return [
            'DB ID',
            'Certificate Number',
            'Participant Name',
            'Passport/NID',
            'Driving License',
            'Company',
            'Training Name',
            'Location',
            'Trainer',
            'Training Date',
            'Issue Date',
            'Expiry Date',
            'Created by',
            'Updated by',
            'Deleted by',
            'Created at',
            'Updated at',
            'Deleted at',
        ];
    }
}