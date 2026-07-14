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
        return Certificate::select(
            'id',
            'certificate_number',
            'certificate_type',
            'participant_name',
            'passport_nid',
            'driving_license',
            'company',
            'training_name',
            'location',
            'trainer',
            'trainer_email',
            'trainer_designation',
            'signatory_name',
            'signatory_email',
            'signatory_designation',
            'signatory_department',
            'training_date',
            'training_end',
            'issue_date',
            'expiry_date',
            'status',
            'created_by',
            'created_by_id',
            'created_at',
            'review_by',
            'review_by_id',
            'reviewed_at',
            'approval_by',
            'approval_by_id',
            'approved_at',
            'updated_by',
            'updated_by_id',
            'updated_at',
            'deleted_by',
            'deleted_by_id',
            'deleted_at'
        )->get();
    }
    
    public function headings(): array
    {
        return [
            'DB ID',
            'Certificate Number',
            'Certificate Type',
            'Participant Name',
            'Passport/NID',
            'Driving License',
            'Company',
            'Training Name',
            'Location',
            'Trainer',
            'Trainer Email',
            'Trainer Designation',
            'Signatory Name',
            'Signatory Email',
            'Signatory Designation',
            'Signatory Department',
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