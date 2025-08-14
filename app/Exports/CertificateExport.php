<?php

namespace App\Exports;

use App\Models\Certificate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

/*
|--------------------------------------------------------------------------
| Certificate Verification System (CVS) 
| TUV Austria Bureau of Inspection & Certification 
| Developed by: Swad Ahmed Mahfuz (Assistant Manager - Sales & Operations, Bangladesh)
| Contact: swad.mahfuz@gmail.com, +1-725-867-7718, +88 01733 023 008
| Project Start: 12 October 2022
|--------------------------------------------------------------------------
*/

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
            'Training Start Date',
            'Training End Date',
            'Issue Date',
            'Expiry Date',
            'Status',
            'Created by',
            'Created by ID',
            'Created at',
            'Review by',
            'Review by ID',
            'Reviewed at',
            'Approval by',
            'Approval by ID',
            'Approved at',
            'Updated by',
            'Updated by ID',
            'Updated at',
            'Deleted by',
            'Deleted by ID',
            'Deleted at',
        ];
    }
}