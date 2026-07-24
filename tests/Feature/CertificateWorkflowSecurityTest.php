<?php

namespace Tests\Feature;

use App\Models\Certificate;
use App\Models\User;
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
}
