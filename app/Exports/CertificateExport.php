<?php

namespace App\Exports;

use App\Models\Certificate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

/*
|--------------------------------------------------------------------------
| Certificate Verification System (CVS)
| TUV Austria Bureau of Inspection & Certification
| Developed by: Swad Ahmed Mahfuz
|--------------------------------------------------------------------------
*/

class CertificateExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithColumnFormatting,
    ShouldAutoSize
{
    /**
     * Retrieve active and soft-deleted certificates.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Certificate::withTrashed()
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Define the exact order of exported values.
     *
     * @param mixed $certificate
     * @return array
     */
    public function map($certificate): array
    {
        return [
            $certificate->id,
            $certificate->certificate_number,
            $certificate->certificate_type,

            $certificate->has_practical
                ? 'Yes'
                : 'No',

            $certificate->is_refresher
                ? 'Yes'
                : 'No',

            $certificate->internal_audit_training
                ? 'Yes'
                : 'No',

            $certificate->online_training
                ? 'Yes'
                : 'No',

            $certificate->participant_name,
            $certificate->passport_nid,
            $certificate->driving_license,
            $certificate->company,
            $certificate->training_name,
            $certificate->location,

            $certificate->trainer,
            $certificate->trainer_email,
            $certificate->trainer_designation,

            $certificate->signatory_name,
            $certificate->signatory_email,
            $certificate->signatory_designation,
            $certificate->signatory_department,

            $certificate->training_date,
            $certificate->training_end,
            $certificate->issue_date,
            $certificate->expiry_date,

            $certificate->created_by,
            $certificate->created_by_id,
            $certificate->review_by,
            $certificate->review_by_id,
            $certificate->approval_by,
            $certificate->approval_by_id,
            $certificate->status,

            $certificate->updated_by,
            $certificate->updated_by_id,
            $certificate->deleted_by,

            optional($certificate->created_at)
                ->format('Y-m-d H:i:s'),

            optional($certificate->reviewed_at)
                ->format('Y-m-d H:i:s'),

            optional($certificate->approved_at)
                ->format('Y-m-d H:i:s'),

            optional($certificate->updated_at)
                ->format('Y-m-d H:i:s'),

            optional($certificate->deleted_at)
                ->format('Y-m-d H:i:s'),

            $certificate->certificate_pdf,
            $certificate->pdf_uploaded_by,
            $certificate->pdf_uploaded_by_id,

            optional($certificate->pdf_uploaded_at)
                ->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Export headings in the same order as map().
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'DB ID',
            'Certificate Number',
            'Certificate Type',
            'Includes Practical Sessions',
            'Refresher Training',
            'Internal Auditor Training',
            'Online Training',
            'Participant Name',
            'Passport/NID',
            'Driving License',
            'Company',
            'Training Name',
            'Location',
            'Trainer',
            'Trainer Email',
            'Trainer Designation',
            'Signatory',
            'Signatory Email',
            'Signatory Designation',
            'Signatory Department',
            'Training Start Date',
            'Training End Date',
            'Issue Date',
            'Expiry Date',
            'Created by',
            'Created by ID',
            'Review by',
            'Review by ID',
            'Approval by',
            'Approval by ID',
            'Status',
            'Updated by',
            'Updated by ID',
            'Deleted by',
            'Created at',
            'Reviewed at',
            'Approved at',
            'Updated at',
            'Deleted at',
            'PDF File',
            'PDF Uploaded by',
            'PDF Uploaded by ID',
            'PDF Uploaded at',
        ];
    }

    /**
     * Preserve certificate and identification numbers as text.
     *
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'I' => NumberFormat::FORMAT_TEXT,
            'J' => NumberFormat::FORMAT_TEXT,
        ];
    }
}