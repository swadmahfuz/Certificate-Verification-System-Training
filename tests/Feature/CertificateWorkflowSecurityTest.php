<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\Certificate;
use App\Models\User;
use App\Services\CertificatePdfService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CertificateWorkflowSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(VerifyCsrfToken::class);

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'filesystems.default' => 'local',
        ]);
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('certificates_training', function (Blueprint $table) {
            $table->increments('id');
            $table->string('certificate_number')->unique();
            $table->string('certificate_type')->nullable();
            $table->string('participant_name');
            $table->string('passport_nid')->nullable();
            $table->string('driving_license')->nullable();
            $table->string('company')->nullable();
            $table->string('training_name')->nullable();
            $table->string('location')->nullable();
            $table->string('trainer')->nullable();
            $table->unsignedInteger('trainer_id')->nullable();
            $table->string('training_date')->nullable();
            $table->string('training_end')->nullable();
            $table->string('issue_date')->nullable();
            $table->string('expiry_date')->nullable();
            $table->string('status');
            $table->string('created_by')->nullable();
            $table->unsignedInteger('created_by_id')->nullable();
            $table->string('review_by')->nullable();
            $table->unsignedInteger('review_by_id')->nullable();
            $table->string('approval_by')->nullable();
            $table->unsignedInteger('approval_by_id')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('updated_by')->nullable();
            $table->unsignedInteger('updated_by_id')->nullable();
            $table->string('certificate_pdf')->nullable();
            $table->string('pdf_uploaded_by')->nullable();
            $table->unsignedInteger('pdf_uploaded_by_id')->nullable();
            $table->timestamp('pdf_uploaded_at')->nullable();
            $table->string('deleted_by')->nullable();
            $table->unsignedInteger('deleted_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('certificates_training_activity_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('event');
            $table->string('subject_type');
            $table->unsignedInteger('subject_id')->nullable();
            $table->unsignedInteger('causer_id')->nullable();
            $table->string('causer_name')->nullable();
            $table->string('description');
            $table->text('properties')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at');
        });
    }

    public function test_review_approve_delete_and_bulk_reject_get_requests()
    {
        $user = User::factory()->create();
        $certificate = Certificate::factory()->create([
            'status' => 'Pending Review',
            'review_by_id' => $user->id,
            'approval_by_id' => $user->id,
        ]);

        $this->actingAs($user)->get('/review-certificate/' . $certificate->id)->assertStatus(405);
        $this->actingAs($user)->get('/approve-certificate/' . $certificate->id)->assertStatus(405);
        $this->actingAs($user)->get('/delete-certificate/' . $certificate->id)->assertStatus(405);
        $this->actingAs($user)->get('/bulk-review')->assertStatus(405);
        $this->actingAs($user)->get('/bulk-approve')->assertStatus(405);
    }

    public function test_review_requires_post_and_assigned_reviewer_id()
    {
        $reviewer = User::factory()->create();
        $other = User::factory()->create();
        $certificate = Certificate::factory()->create([
            'status' => 'Pending Review',
            'review_by_id' => $reviewer->id,
            'review_by' => $reviewer->name,
        ]);

        $this->actingAs($other)
            ->post(route('certificate.review', $certificate->id))
            ->assertRedirect();

        $this->assertEquals('Pending Review', $certificate->fresh()->status);

        $this->actingAs($reviewer)
            ->post(route('certificate.review', $certificate->id))
            ->assertRedirect('/view-certificate/' . $certificate->id);

        $this->assertEquals('Pending Approval', $certificate->fresh()->status);
    }

    public function test_uploaded_pdf_is_stored_privately()
    {
        Storage::fake('local');

        $user = User::factory()->create();
        $certificate = Certificate::factory()->create([
            'created_by_id' => $user->id,
            'created_by' => $user->name,
            'certificate_pdf' => null,
        ]);

        $file = UploadedFile::fake()->create('cert.pdf', 100, 'application/pdf');

        $this->actingAs($user)
            ->post(route('certificate.uploadPdf', $certificate->id), [
                'certificate_pdf' => $file,
            ])
            ->assertRedirect();

        $certificate->refresh();
        $this->assertNotEmpty($certificate->certificate_pdf);
        Storage::disk('local')->assertExists('certificate-pdfs/' . $certificate->certificate_pdf);
        $this->assertFileDoesNotExist(public_path('Certificate PDFs/' . $certificate->certificate_pdf));
    }

    public function test_selected_bulk_routes_reject_get_requests()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/certificates/bulk-review')->assertStatus(405);
        $this->actingAs($user)->get('/certificates/bulk-approve')->assertStatus(405);
        $this->actingAs($user)->get('/certificates/bulk-delete')->assertStatus(405);
        $this->actingAs($user)->get('/certificates/bulk-pdf')->assertStatus(405);
    }

    public function test_selected_bulk_review_only_updates_eligible_assignments()
    {
        $reviewer = User::factory()->create();
        $otherReviewer = User::factory()->create();
        $eligible = Certificate::factory()->create([
            'status' => 'Pending Review',
            'review_by_id' => $reviewer->id,
        ]);
        $wrongReviewer = Certificate::factory()->create([
            'status' => 'Pending Review',
            'review_by_id' => $otherReviewer->id,
        ]);
        $wrongStatus = Certificate::factory()->create([
            'status' => 'Pending Approval',
            'review_by_id' => $reviewer->id,
        ]);

        $this->actingAs($reviewer)
            ->post(route('certificates.bulkReviewSelected'), [
                'certificate_ids' => [
                    $eligible->id,
                    $wrongReviewer->id,
                    $wrongStatus->id,
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('bulk_action_completed', true);

        $this->assertEquals('Pending Approval', $eligible->fresh()->status);
        $this->assertEquals('Pending Review', $wrongReviewer->fresh()->status);
        $this->assertEquals('Pending Approval', $wrongStatus->fresh()->status);
    }

    public function test_selected_bulk_approve_only_updates_eligible_assignments()
    {
        $approver = User::factory()->create();
        $otherApprover = User::factory()->create();
        $eligible = Certificate::factory()->create([
            'status' => 'Pending Approval',
            'approval_by_id' => $approver->id,
        ]);
        $wrongApprover = Certificate::factory()->create([
            'status' => 'Pending Approval',
            'approval_by_id' => $otherApprover->id,
        ]);
        $wrongStatus = Certificate::factory()->create([
            'status' => 'Pending Review',
            'approval_by_id' => $approver->id,
        ]);

        $this->actingAs($approver)
            ->post(route('certificates.bulkApproveSelected'), [
                'certificate_ids' => [
                    $eligible->id,
                    $wrongApprover->id,
                    $wrongStatus->id,
                ],
            ])
            ->assertRedirect()
            ->assertSessionHas('bulk_action_completed', true);

        $this->assertEquals('Approved', $eligible->fresh()->status);
        $this->assertEquals('Pending Approval', $wrongApprover->fresh()->status);
        $this->assertEquals('Pending Review', $wrongStatus->fresh()->status);
    }

    public function test_selected_bulk_delete_only_soft_deletes_requested_certificates()
    {
        $user = User::factory()->create();
        $selected = Certificate::factory()->create([
            'certificate_number' => 'TR-SELECTED',
        ]);
        $unselected = Certificate::factory()->create([
            'certificate_number' => 'TR-UNSELECTED',
        ]);

        $this->actingAs($user)
            ->post(route('certificates.bulkDelete'), [
                'certificate_ids' => [$selected->id],
            ])
            ->assertRedirect()
            ->assertSessionHas('bulk_action_completed', true);

        $this->assertSoftDeleted('certificates_training', ['id' => $selected->id]);
        $this->assertDatabaseHas('certificates_training', [
            'id' => $selected->id,
            'certificate_number' => 'TR-SELECTED (Deleted)',
            'status' => 'Deleted',
            'deleted_by_id' => $user->id,
        ]);
        $this->assertDatabaseHas('certificates_training', [
            'id' => $unselected->id,
            'deleted_at' => null,
        ]);
    }

    public function test_selected_bulk_actions_require_certificate_ids()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('certificates.index'))
            ->post(route('certificates.bulkDelete'), [])
            ->assertRedirect(route('certificates.index'))
            ->assertSessionHasErrors('certificate_ids');
    }

    public function test_selected_bulk_pdf_download_contains_only_eligible_certificates()
    {
        if (!class_exists(\ZipArchive::class)) {
            $this->markTestSkipped('The PHP ZIP extension is not available.');
        }

        $user = User::factory()->create();
        $eligible = Certificate::factory()->approved()->create([
            'certificate_number' => 'TR-PDF-ELIGIBLE',
            'certificate_type' => 'Certificate',
            'trainer_id' => 1,
        ]);
        $ineligible = Certificate::factory()->create([
            'certificate_number' => 'TR-PDF-INELIGIBLE',
            'status' => 'Pending Review',
            'certificate_type' => 'Certificate',
            'trainer_id' => 1,
        ]);

        $pdfService = \Mockery::mock(CertificatePdfService::class);
        $pdfService->shouldReceive('generateTestPdf')
            ->once()
            ->with(\Mockery::on(function ($certificate) use ($eligible) {
                return $certificate->id === $eligible->id;
            }))
            ->andReturn('%PDF-1.4 test');
        $this->app->instance(CertificatePdfService::class, $pdfService);

        $response = $this->actingAs($user)
            ->post(route('certificates.bulkPdf'), [
                'certificate_ids' => [$eligible->id, $ineligible->id],
            ])
            ->assertOk()
            ->assertHeader('content-disposition');

        $archivePath = $response->baseResponse->getFile()->getPathname();
        $zip = new \ZipArchive();
        $this->assertTrue($zip->open($archivePath) === true);
        $this->assertEquals(1, $zip->numFiles);
        $this->assertStringContainsString('TR-PDF-ELIGIBLE', $zip->getNameIndex(0));
        $zip->close();
        @unlink($archivePath);
    }
}
