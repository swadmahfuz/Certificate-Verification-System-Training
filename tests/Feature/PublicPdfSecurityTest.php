<?php

namespace Tests\Feature;

use App\Models\Certificate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPdfSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pdf_requires_approved_status(): void
    {
        $certificate = Certificate::factory()->create([
            'status' => 'Pending Review',
            'certificate_pdf' => 'sample.pdf',
        ]);

        $response = $this->get(route('certificate.publicPdf', $certificate->id));

        $response->assertStatus(404);
    }
}
