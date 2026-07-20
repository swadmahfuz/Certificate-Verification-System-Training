<?php

namespace App\Services;

use App\Models\Certificate;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Tfpdf\Fpdi;

class CertificatePdfService
{
    /**
     * Generate a portrait A4 certificate PDF in memory.
     *
     * @param  \App\Models\Certificate  $certificate
     * @return string
     */
    public function generateTestPdf(Certificate $certificate)
    {
        $pdf = $this->createPdf($certificate);

        $this->drawCertificateTitle(
            $pdf,
            $certificate->certificate_type
        );

        $this->drawCertificateBody(
            $pdf,
            $certificate
        );

        $this->drawCertificateDetails(
            $pdf,
            $certificate
        );

        $this->drawVerificationQrCode(
            $pdf,
            $certificate
        );

        $this->drawSignatureBlocks(
            $pdf,
            $certificate
        );

        /// Return the PDF without permanently storing it.
        return $pdf->Output('S');
    }

    /**
     * Create the PDF, register the fonts and apply the template.
     *
     * @param  \App\Models\Certificate  $certificate
     * @return \setasign\Fpdi\Tfpdf\Fpdi
     */
    private function createPdf(Certificate $certificate)
    {
        $fontDirectory = Storage::path(
            'certificate-fonts'
        ) . DIRECTORY_SEPARATOR;

        $requiredFonts = [
            'constan.ttf',
            'constanb.ttf',
            'constani.ttf',
            'constanz.ttf',
            'pala.ttf',
            'palab.ttf',
            'palai.ttf',
            'palabi.ttf',
        ];

        foreach ($requiredFonts as $requiredFont)
        {
            if (!file_exists($fontDirectory . $requiredFont))
            {
                throw new \RuntimeException(
                    'Certificate font was not found: ' .
                    $requiredFont
                );
            }
        }

        if (!defined('_SYSTEM_TTFONTS'))
        {
            define(
                '_SYSTEM_TTFONTS',
                $fontDirectory
            );
        }

        $pdf = new Fpdi(
            'P',
            'mm',
            'A4'
        );

        /// Constantia font family.
        $pdf->AddFont(
            'Constantia',
            '',
            'constan.ttf',
            true
        );

        $pdf->AddFont(
            'Constantia',
            'B',
            'constanb.ttf',
            true
        );

        $pdf->AddFont(
            'Constantia',
            'I',
            'constani.ttf',
            true
        );

        $pdf->AddFont(
            'Constantia',
            'BI',
            'constanz.ttf',
            true
        );

        /// Palatino Linotype font family.
        $pdf->AddFont(
            'Palatino',
            '',
            'pala.ttf',
            true
        );

        $pdf->AddFont(
            'Palatino',
            'B',
            'palab.ttf',
            true
        );

        $pdf->AddFont(
            'Palatino',
            'I',
            'palai.ttf',
            true
        );

        $pdf->AddFont(
            'Palatino',
            'BI',
            'palabi.ttf',
            true
        );

        /// PDF document information.
        $pdf->SetTitle(
            'Training Certificate - ' .
            $certificate->certificate_number
        );

        $pdf->SetAuthor(
            'TUV Austria BIC Bangladesh'
        );

        $pdf->SetCreator(
            'Training Certificate Verification System'
        );

        $pdf->SetMargins(
            0,
            0,
            0
        );

        $pdf->SetAutoPageBreak(false);

        /// Load the blank template.
        $templatePath = Storage::path(
            'certificate-templates/certificate-template.pdf'
        );

        if (!file_exists($templatePath))
        {
            throw new \RuntimeException(
                'Certificate PDF template was not found.'
            );
        }

        $pdf->setSourceFile(
            $templatePath
        );

        $templatePage = $pdf->importPage(1);

        $pdf->AddPage(
            'P',
            'A4'
        );

        $pdf->useTemplate(
            $templatePage,
            0,
            0,
            210,
            297
        );

        $pdf->SetTextColor(
            0,
            0,
            0
        );

        return $pdf;
    }

    /**
     * Draw the certificate title using one or two lines.
     *
     * @param  \setasign\Fpdi\Tfpdf\Fpdi  $pdf
     * @param  string|null  $certificateType
     * @return void
     */
    private function drawCertificateTitle(
        Fpdi $pdf,
        $certificateType
    ) {
        $titleLines = $this->getCertificateTitleLines(
            $certificateType
        );

        $pdf->SetFont(
            'Constantia',
            'BI',
            28
        );

        if (count($titleLines) === 1)
        {
            $pdf->SetXY(
                20,
                38.5
            );

            $pdf->Cell(
                170,
                13,
                $titleLines[0],
                0,
                0,
                'C'
            );

            return;
        }

        $pdf->SetXY(
            20,
            38
        );

        $pdf->Cell(
            170,
            13,
            $titleLines[0],
            0,
            0,
            'C'
        );

        $pdf->SetXY(
            20,
            53.8
        );

        $pdf->Cell(
            170,
            13,
            $titleLines[1],
            0,
            0,
            'C'
        );
    }

    /**
     * Draw the participant and training information.
     *
     * @param  \setasign\Fpdi\Tfpdf\Fpdi  $pdf
     * @param  \App\Models\Certificate  $certificate
     * @return void
     */
    private function drawCertificateBody(
        Fpdi $pdf,
        Certificate $certificate
    ) {
        /// Introductory sentence.
        $pdf->SetFont(
            'Palatino',
            'I',
            13
        );

        $this->drawCenteredLine(
            $pdf,
            'This is to certify that',
            77,
            170,
            20
        );

        /// Participant name.
        $this->drawCenteredFittedLine(
            $pdf,
            strtoupper(
                trim(
                    (string) $certificate->participant_name
                )
            ),
            85,
            'Palatino',
            'B',
            17,
            12,
            165
        );

        /// Participant identification.
        $this->drawCenteredFittedLine(
            $pdf,
            $this->getIdentificationLine(
                $certificate
            ),
            94.2,
            'Palatino',
            '',
            12,
            9,
            165
        );

        /// Company introduction.
        $pdf->SetFont(
            'Palatino',
            '',
            13
        );

        $this->drawCenteredLine(
            $pdf,
            'of',
            101.8,
            170,
            20
        );

        /// Company name.
        $this->drawCenteredFittedLine(
            $pdf,
            trim(
                (string) $certificate->company
            ),
            109,
            'Palatino',
            'B',
            15,
            10,
            170
        );

        /// Certificate-type-specific wording.
        $this->drawCenteredFittedLine(
            $pdf,
            $this->getCompletionWording(
                $certificate
            ),
            121.8,
            'Palatino',
            'I',
            12,
            9,
            178
        );

        /// Training or assessment title.
        $trainingName = trim(
            (string) $certificate->training_name
        );

        /// Remove existing quotation marks to prevent duplication.
        $trainingName = trim(
            $trainingName,
            " \t\n\r\0\x0B\"'“”‘’"
        );

        /// Add consistent quotation marks for every certificate type.
        $trainingName =
            '“' .
            $trainingName .
            '”';

        $this->drawCenteredFittedLine(
            $pdf,
            $trainingName,
            134.4,
            'Palatino',
            'B',
            14,
            9,
            178
        );

        /// Training date or date range.
        $this->drawCenteredFittedLine(
            $pdf,
            $this->getTrainingDateLine(
                $certificate->training_date,
                $certificate->training_end
            ),
            150.5,
            'Palatino',
            'B',
            12,
            9,
            170
        );

        /// Display the training location only for physical training.
        if (!(bool) $certificate->online_training)
        {
            $pdf->SetFont(
                'Palatino',
                '',
                11
            );

            $this->drawCenteredLine(
                $pdf,
                'at',
                157.4,
                170,
                20
            );

            $this->drawCenteredFittedLine(
                $pdf,
                trim(
                    (string) $certificate->location
                ),
                163.4,
                'Palatino',
                '',
                11.5,
                8.5,
                178
            );
        }

        /// Move the organizer upward for online training.
        $organizerLabelY = (bool) $certificate->online_training
            ? 163.4
            : 175.1;

        $organizerNameY = (bool) $certificate->online_training
            ? 169.5
            : 181.2;

        /// Organizer.
        $pdf->SetFont(
            'Palatino',
            'I',
            12
        );

        $this->drawCenteredLine(
            $pdf,
            'Organized by',
            $organizerLabelY,
            170,
            20
        );

        $this->drawCenteredFittedLine(
            $pdf,
            'TUV Austria Bureau of Inspection and Certification (Pvt.) Ltd.',
            $organizerNameY,
            'Palatino',
            'B',
            11.5,
            9,
            178
        );
    }

    /**
     * Draw certificate number and validity details.
     *
     * @param  \setasign\Fpdi\Tfpdf\Fpdi  $pdf
     * @param  \App\Models\Certificate  $certificate
     * @return void
     */
    private function drawCertificateDetails(
        Fpdi $pdf,
        Certificate $certificate
    ) {
        $detailLines = [
            'Certificate No: ' .
                $certificate->certificate_number,

            'Issued Date: ' .
                $this->formatMonthDate(
                    $certificate->issue_date
                ),
        ];

        if (!empty($certificate->expiry_date))
        {
            $detailLines[] =
                'Valid Till: ' .
                $this->formatMonthDate(
                    $certificate->expiry_date
                );
        }

        $pdf->SetFont(
            'Palatino',
            'BI',
            9.5
        );

        $pdf->SetXY(
            31.5,
            205.2
        );

        $pdf->MultiCell(
            100,
            4.3,
            implode(
                "\n",
                $detailLines
            ),
            0,
            'L'
        );
    }

    /**
     * Generate and insert the verification QR code.
     *
     * @param  \setasign\Fpdi\Tfpdf\Fpdi  $pdf
     * @param  \App\Models\Certificate  $certificate
     * @return void
     */
    private function drawVerificationQrCode(
        Fpdi $pdf,
        Certificate $certificate
    ) {
        $verificationUrl = route(
            'certificate.search',
            [
                'search' =>
                    $certificate->certificate_number
            ]
        );

        $qrApiUrl =
            'https://api.qrserver.com/v1/create-qr-code/' .
            '?size=300x300&format=png&data=' .
            urlencode(
                $verificationUrl
            );

        $temporaryQrPath = tempnam(
            sys_get_temp_dir(),
            'certificate-qr-'
        );

        try
        {
            $qrResponse = Http::timeout(20)
                ->get(
                    $qrApiUrl
                );

            if (
                !$qrResponse->successful() ||
                empty($qrResponse->body())
            ) {
                throw new \RuntimeException(
                    'The QR-code service did not return a valid image.'
                );
            }

            file_put_contents(
                $temporaryQrPath,
                $qrResponse->body()
            );

            $pdf->Image(
                $temporaryQrPath,
                167,
                201,
                25.5,
                25.5,
                'PNG'
            );
        }
        catch (\Throwable $exception)
        {
            throw new \RuntimeException(
                'The certificate QR code could not be generated.',
                0,
                $exception
            );
        }
        finally
        {
            if (
                !empty($temporaryQrPath) &&
                file_exists($temporaryQrPath)
            ) {
                unlink(
                    $temporaryQrPath
                );
            }
        }
    }

    /**
     * Draw trainer and optional signatory blocks.
     *
     * @param  \setasign\Fpdi\Tfpdf\Fpdi  $pdf
     * @param  \App\Models\Certificate  $certificate
     * @return void
     */
    private function drawSignatureBlocks(
        Fpdi $pdf,
        Certificate $certificate
    ) {
        /// Signatory on left and trainer on right.
        if (!empty($certificate->signatory_name))
        {
            $this->drawSignatureBlock(
                $pdf,
                30.5,
                52,
                $certificate->signatory_signature_path,
                $certificate->signatory_name,
                $certificate->signatory_designation,
                $certificate->signatory_department
                    ? $certificate->signatory_department
                    : 'Business Assurance & Training'
            );

            $this->drawSignatureBlock(
                $pdf,
                123,
                52,
                $certificate->trainer_signature_path,
                $certificate->trainer,
                $certificate->trainer_designation,
                'Business Assurance & Training'
            );

            return;
        }

        /// Trainer-only certificate — use the left signature position.
        $this->drawSignatureBlock(
            $pdf,
            30.5,
            52,
            $certificate->trainer_signature_path,
            $certificate->trainer,
            $certificate->trainer_designation,
            'Business Assurance & Training'
        );

    }

    /**
     * Draw one signature block.
     *
     * @param  \setasign\Fpdi\Tfpdf\Fpdi  $pdf
     * @param  float  $x
     * @param  float  $width
     * @param  string|null  $signaturePath
     * @param  string|null  $name
     * @param  string|null  $designation
     * @param  string|null  $department
     * @return void
     */
    private function drawSignatureBlock(
        Fpdi $pdf,
        $x,
        $width,
        $signaturePath,
        $name,
        $designation,
        $department
    ) {
        $this->drawSignatureImage(
            $pdf,
            $signaturePath,
            $x,
            228.2,
            $width,
            12.5
        );

        /// Signature line.
        $pdf->SetDrawColor(
            0,
            0,
            0
        );

        $pdf->SetLineWidth(
            0.18
        );

        $pdf->Line(
            $x,
            241,
            $x + $width,
            241
        );

        $details = [];

        if (!empty($name))
        {
            $details[] = trim(
                (string) $name
            );
        }

        if (!empty($designation))
        {
            $details[] = trim(
                (string) $designation
            );
        }

        if (!empty($department))
        {
            $details[] = trim(
                (string) $department
            );
        }

        $details[] =
            'TÜV Austria BIC (Pvt) Ltd.';

        $lineY = 242.2;

        foreach ($details as $detail)
        {
            $this->drawLeftFittedLine(
                $pdf,
                $detail,
                $x,
                $lineY,
                $width + 8,
                'Palatino',
                '',
                9.3,
                6.5
            );

            $lineY += 4;
        }
    }

    /**
     * Draw a signature while preserving the aspect ratio.
     *
     * @param  \setasign\Fpdi\Tfpdf\Fpdi  $pdf
     * @param  string|null  $signaturePath
     * @param  float  $blockX
     * @param  float  $topY
     * @param  float  $blockWidth
     * @param  float  $maximumHeight
     * @return void
     */
    private function drawSignatureImage(
        Fpdi $pdf,
        $signaturePath,
        $blockX,
        $topY,
        $blockWidth,
        $maximumHeight
    ) {
        if (
            empty($signaturePath) ||
            !Storage::exists($signaturePath)
        ) {
            throw new \RuntimeException(
                'A required certificate signature image was not found.'
            );
        }

        $absolutePath = Storage::path(
            $signaturePath
        );

        $extension = strtolower(
            pathinfo(
                $absolutePath,
                PATHINFO_EXTENSION
            )
        );

        if (
            !in_array(
                $extension,
                [
                    'png',
                    'jpg',
                    'jpeg'
                ]
            )
        ) {
            throw new \RuntimeException(
                'A certificate signature uses an unsupported image format.'
            );
        }

        $imageSize = @getimagesize(
            $absolutePath
        );

        if (
            !$imageSize ||
            empty($imageSize[0]) ||
            empty($imageSize[1])
        ) {
            throw new \RuntimeException(
                'A certificate signature image is invalid.'
            );
        }

        $maximumWidth = min(
            38,
            $blockWidth - 4
        );

        $imageRatio =
            $imageSize[0] /
            $imageSize[1];

        $displayWidth =
            $maximumWidth;

        $displayHeight =
            $displayWidth /
            $imageRatio;

        if ($displayHeight > $maximumHeight)
        {
            $displayHeight =
                $maximumHeight;

            $displayWidth =
                $displayHeight *
                $imageRatio;
        }

        $imageX =
            $blockX +
            (
                (
                    $blockWidth -
                    $displayWidth
                ) / 2
            );

        $imageY =
            $topY +
            (
                $maximumHeight -
                $displayHeight
            );

        $pdf->Image(
            $absolutePath,
            $imageX,
            $imageY,
            $displayWidth,
            $displayHeight
        );
    }

    /**
     * Draw one left-aligned line and reduce its font size when necessary.
     *
     * @param  \setasign\Fpdi\Tfpdf\Fpdi  $pdf
     * @param  string  $text
     * @param  float  $x
     * @param  float  $y
     * @param  float  $maximumWidth
     * @param  string  $fontFamily
     * @param  string  $fontStyle
     * @param  float  $maximumFontSize
     * @param  float  $minimumFontSize
     * @return void
     */
    private function drawLeftFittedLine(
        Fpdi $pdf,
        $text,
        $x,
        $y,
        $maximumWidth,
        $fontFamily,
        $fontStyle,
        $maximumFontSize,
        $minimumFontSize
    ) {
        $text = trim(
            (string) $text
        );

        $fontSize =
            $maximumFontSize;

        do
        {
            $pdf->SetFont(
                $fontFamily,
                $fontStyle,
                $fontSize
            );

            if (
                $pdf->GetStringWidth($text) <=
                $maximumWidth
            ) {
                break;
            }

            $fontSize -= 0.2;
        }
        while (
            $fontSize >=
            $minimumFontSize
        );

        $pdf->SetXY(
            $x,
            $y
        );

        $pdf->Cell(
            $maximumWidth,
            4,
            $text,
            0,
            0,
            'L'
        );
    }

    /**
     * Draw one centred line.
     *
     * @param  \setasign\Fpdi\Tfpdf\Fpdi  $pdf
     * @param  string  $text
     * @param  float  $y
     * @param  float  $width
     * @param  float  $x
     * @return void
     */
    private function drawCenteredLine(
        Fpdi $pdf,
        $text,
        $y,
        $width,
        $x
    ) {
        $pdf->SetXY(
            $x,
            $y
        );

        $pdf->Cell(
            $width,
            7,
            $text,
            0,
            0,
            'C'
        );
    }

    /**
     * Draw a centred line and reduce the font size when necessary.
     *
     * @param  \setasign\Fpdi\Tfpdf\Fpdi  $pdf
     * @param  string  $text
     * @param  float  $y
     * @param  string  $fontFamily
     * @param  string  $fontStyle
     * @param  float  $maximumFontSize
     * @param  float  $minimumFontSize
     * @param  float  $maximumWidth
     * @return void
     */
    private function drawCenteredFittedLine(
        Fpdi $pdf,
        $text,
        $y,
        $fontFamily,
        $fontStyle,
        $maximumFontSize,
        $minimumFontSize,
        $maximumWidth
    ) {
        $text = trim(
            (string) $text
        );

        $fontSize =
            $maximumFontSize;

        do
        {
            $pdf->SetFont(
                $fontFamily,
                $fontStyle,
                $fontSize
            );

            if (
                $pdf->GetStringWidth($text) <=
                $maximumWidth
            ) {
                break;
            }

            $fontSize -= 0.5;
        }
        while (
            $fontSize >=
            $minimumFontSize
        );

        $x =
            (
                210 -
                $maximumWidth
            ) / 2;

        $pdf->SetXY(
            $x,
            $y
        );

        $pdf->Cell(
            $maximumWidth,
            7,
            $text,
            0,
            0,
            'C'
        );
    }

    /**
     * Get the title lines for the certificate type.
     *
     * @param  string|null  $certificateType
     * @return array
     */
    private function getCertificateTitleLines(
        $certificateType
    ) {
        $normalizedType = strtolower(
            trim(
                (string) $certificateType
            )
        );

        if (
            $normalizedType ===
            'certificate of achievement'
        ) {
            return [
                'CERTIFICATE OF',
                'ACHIEVEMENT'
            ];
        }

        if (
            $normalizedType ===
            'certificate of competency'
        ) {
            return [
                'CERTIFICATE OF',
                'COMPETENCY'
            ];
        }

        if (
            $normalizedType ===
            'certificate of attendance'
        ) {
            return [
                'CERTIFICATE OF',
                'ATTENDANCE'
            ];
        }

        return [
            'CERTIFICATE'
        ];
    }

    /**
     * Get the body wording according to certificate type
     * and training classification.
     *
     * @param  \App\Models\Certificate  $certificate
     * @return string
     */
    private function getCompletionWording(
        Certificate $certificate
    ) {
        $isOnline = (bool) $certificate->online_training;

        /*
        * Internal Auditor wording overrides certificate type,
        * refresher selection and practical-session selection.
        */
        if ((bool) $certificate->internal_audit_training)
        {
            if ($isOnline)
            {
                return
                    'Has successfully completed the online Internal Auditor training program on';
            }

            return
                'Has successfully completed the Internal Auditor training program on';
        }

        $normalizedType = strtolower(
            trim(
                (string) $certificate->certificate_type
            )
        );

        $hasPractical = (bool) $certificate->has_practical;
        $isRefresher = (bool) $certificate->is_refresher;

        /*
        * Certificate of Competency uses
        * assessment-specific wording.
        */
        if (
            $normalizedType ===
            'certificate of competency'
        ) {
            if ($isOnline)
            {
                $assessmentDescription = $isRefresher
                    ? 'online refresher assessment'
                    : 'online assessment';
            }
            else
            {
                $assessmentDescription = $isRefresher
                    ? 'refresher assessment'
                    : 'assessment';
            }

            if ($hasPractical)
            {
                $assessmentDescription .=
                    ' (Theory & Practical)';
            }

            return
                'Has participated in and successfully completed the ' .
                $assessmentDescription .
                ' on';
        }

        /*
        * Build the training-program description for
        * Certificate, Achievement and Attendance.
        */
        if ($isOnline)
        {
            $programDescription = $isRefresher
                ? 'online refresher training program'
                : 'online training program';
        }
        else
        {
            $programDescription = $isRefresher
                ? 'refresher training program'
                : 'training program';
        }

        if ($hasPractical)
        {
            $programDescription .=
                ' (Theory & Practical)';
        }

        /*
        * Certificate of Attendance does not use
        * "successfully completed".
        */
        if (
            $normalizedType ===
            'certificate of attendance'
        ) {
            return
                'Has participated in the ' .
                $programDescription .
                ' on';
        }

        return
            'Has participated in and successfully completed the ' .
            $programDescription .
            ' on';
    }

    /**
     * Determine whether it is a competency certificate.
     *
     * @param  string|null  $certificateType
     * @return bool
     */
    private function isCompetencyCertificate(
        $certificateType
    ) {
        return strtolower(
            trim(
                (string) $certificateType
            )
        ) === 'certificate of competency';
    }

    /**
     * Build the participant identification line.
     *
     * @param  \App\Models\Certificate  $certificate
     * @return string
     */

    private function getIdentificationLine(
    Certificate $certificate
    ) {
        $passportNid = trim(
            (string) $certificate->passport_nid
        );

        $drivingLicense = trim(
            (string) $certificate->driving_license
        );

        if (
            !empty($passportNid) &&
            !empty($drivingLicense)
        ) {
            return
                'NID/Passport: ' .
                $passportNid .
                ' | Driving License: ' .
                $drivingLicense;
        }

        if (!empty($passportNid))
        {
            return
                'NID/Passport: ' .
                $passportNid;
        }

        if (!empty($drivingLicense))
        {
            return
                'Driving License: ' .
                $drivingLicense;
        }

        return 'Identification: N/A';
    }

    /**
     * Build the training-date line.
     *
     * @param  mixed  $trainingDate
     * @param  mixed  $trainingEnd
     * @return string
     */
    private function getTrainingDateLine(
        $trainingDate,
        $trainingEnd
    ) {
        $startDate = Carbon::parse(
            $trainingDate
        );

        $endDate = !empty($trainingEnd)
            ? Carbon::parse($trainingEnd)
            : $startDate->copy();

        if ($startDate->isSameDay($endDate))
        {
            return
                'Held on: ' .
                $startDate->format(
                    'jS F, Y'
                );
        }

        if (
            $startDate->year ===
                $endDate->year &&
            $startDate->month ===
                $endDate->month
        ) {
            return
                'Held on: ' .
                $startDate->format('jS') .
                ' - ' .
                $endDate->format(
                    'jS F, Y'
                );
        }

        if (
            $startDate->year ===
            $endDate->year
        ) {
            return
                'Held on: ' .
                $startDate->format(
                    'jS F'
                ) .
                ' - ' .
                $endDate->format(
                    'jS F, Y'
                );
        }

        return
            'Held on: ' .
            $startDate->format(
                'jS F, Y'
            ) .
            ' - ' .
            $endDate->format(
                'jS F, Y'
            );
    }

    /**
     * Format a date as July 04, 2026.
     *
     * @param  mixed  $date
     * @return string
     */
    private function formatMonthDate(
        $date
    ) {
        return Carbon::parse(
            $date
        )->format(
            'F d, Y'
        );
    }
}