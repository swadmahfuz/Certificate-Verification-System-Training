<?php

namespace App\Imports;

use App\Models\Certificate;
use App\Models\Signatory;
use App\Models\Trainer;
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
        // Check if the user exists in the database and then use the email
        // to get the user ID and name to store in the database.
        $createdUser = User::where(
            'email',
            $row['created_by_email'] ?? null
        )->first();

        $reviewUser = User::where(
            'email',
            $row['review_by_email'] ?? null
        )->first();

        $approvalUser = User::where(
            'email',
            $row['approval_by_email'] ?? null
        )->first();

        // Use the trainer email to retrieve the correct active trainer
        // and signature details.
        $trainerEmail = strtolower(
            trim($row['trainer_email'] ?? '')
        );

        $trainer = Trainer::where('email', $trainerEmail)
            ->where('is_active', true)
            ->first();

        if (!$trainer) {
            throw new \Exception(
                'No active trainer was found for email: ' .
                $trainerEmail
            );
        }

        if (empty($trainer->signature_path)) {
            throw new \Exception(
                'The trainer does not have a signature: ' .
                $trainerEmail
            );
        }

        /// Retrieve the optional signatory using the email address given in the Excel file.
        $signatory = null;

        $signatoryEmail = strtolower(
            trim($row['signatory_email'] ?? '')
        );

        if (!empty($signatoryEmail)) {
            $signatory = Signatory::where(
                'email',
                $signatoryEmail
            )
                ->where('is_active', true)
                ->first();

            if (!$signatory) {
                throw new \Exception(
                    'No active signatory was found for email: ' .
                    $signatoryEmail
                );
            }

            if (empty($signatory->signature_path)) {
                throw new \Exception(
                    'The signatory does not have a signature: ' .
                    $signatoryEmail
                );
            }
        }

        /// Validate the certificate type.
        $certificateType = trim(
            $row['certificate_type'] ?? ''
        );

        $allowedCertificateTypes = [
            'Certificate',
            'Certificate of Achievement',
            'Certificate of Competency',
            'Certificate of Attendance',
        ];

        if (!in_array(
            $certificateType,
            $allowedCertificateTypes,
            true
        )) {
            throw new \Exception(
                'Invalid certificate type: ' .
                $certificateType
            );
        }

        /*
         * Read the practical-session classification.
         *
         * The first heading corresponds to:
         * "Includes Practical Sessions"
         *
         * The second heading is also supported for compatibility:
         * "has_practical"
         */
        $hasPractical = $this->parseBoolean(
            $row['includes_practical_sessions']
                ?? $row['has_practical']
                ?? null,
            'Includes Practical Sessions',
            $row['certificate_number'] ?? ''
        );

        /*
         * Read the refresher-training classification.
         *
         * The first heading corresponds to:
         * "Refresher Training"
         *
         * The second heading is also supported for compatibility:
         * "is_refresher"
         */
        $isRefresher = $this->parseBoolean(
            $row['refresher_training']
                ?? $row['is_refresher']
                ?? null,
            'Refresher Training',
            $row['certificate_number'] ?? ''
        );

        $loggedInUser = Auth::user();

        return new Certificate([
            'certificate_number' => $row['certificate_number'],
            'certificate_type' => $certificateType,

            'has_practical' => $hasPractical,
            'is_refresher' => $isRefresher,

            'participant_name' => $row['participant_name'],
            'passport_nid' => $row['passport_nid'],
            'driving_license' => $row['driving_license'] ?? null,
            'company' => $row['company'] ?? null,
            'training_name' => $row['training_name'],
            'location' => $row['location'],

            'trainer_id' => $trainer->id,
            'trainer' => $trainer->name,
            'trainer_email' => $trainer->email,
            'trainer_designation' => $trainer->designation,
            'trainer_signature_path' => $trainer->signature_path,

            'signatory_id' => $signatory
                ? $signatory->id
                : null,

            'signatory_name' => $signatory
                ? $signatory->name
                : null,

            'signatory_email' => $signatory
                ? $signatory->email
                : null,

            'signatory_designation' => $signatory
                ? $signatory->designation
                : null,

            'signatory_department' => $signatory
                ? $signatory->department
                : null,

            'signatory_signature_path' => $signatory
                ? $signatory->signature_path
                : null,

            'training_date' => $row['training_date'],
            'training_end' => $row['training_end'],
            'issue_date' => $row['issue_date'],
            'expiry_date' => $row['expiry_date'] ?? null,

            'status' => 'Pending Review',

            'created_by' => $createdUser
                ? $createdUser->name
                : null,

            'created_by_id' => $createdUser
                ? $createdUser->id
                : null,

            'created_at' => Carbon::now(),

            'review_by' => $reviewUser
                ? $reviewUser->name
                : null,

            'review_by_id' => $reviewUser
                ? $reviewUser->id
                : null,

            'approval_by' => $approvalUser
                ? $approvalUser->name
                : null,

            'approval_by_id' => $approvalUser
                ? $approvalUser->id
                : null,

            'updated_by' => $loggedInUser
                ? $loggedInUser->name
                : null,

            'updated_by_id' => $loggedInUser
                ? $loggedInUser->id
                : null,

            'updated_at' => Carbon::now(),
        ]);
    }

    /**
     * Convert spreadsheet values into boolean values.
     *
     * Accepted true values:
     * Yes, Y, True, 1
     *
     * Accepted false values:
     * No, N, False, 0 or blank
     */
    private function parseBoolean(
        $value,
        $fieldName,
        $certificateNumber
    ) {
        if (
            $value === null ||
            trim((string) $value) === ''
        ) {
            return false;
        }

        $normalizedValue = strtolower(
            trim((string) $value)
        );

        if (in_array(
            $normalizedValue,
            ['yes', 'y', 'true', '1'],
            true
        )) {
            return true;
        }

        if (in_array(
            $normalizedValue,
            ['no', 'n', 'false', '0'],
            true
        )) {
            return false;
        }

        throw new \Exception(
            'Invalid value for "' .
            $fieldName .
            '" in certificate ' .
            $certificateNumber .
            '. Use Yes or No.'
        );
    }
}