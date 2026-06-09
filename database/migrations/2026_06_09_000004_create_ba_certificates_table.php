<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ba_certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ba_client_id');
            $table->unsignedInteger('ba_standard_id')->nullable();
            $table->unsignedInteger('ba_accreditation_body_id')->nullable();

            $table->string('certificate_number')->nullable()->unique();
            $table->text('certificate_scope')->nullable();
            $table->date('certificate_issue_date')->nullable();
            $table->date('certificate_expiry_date')->nullable();
            $table->string('certification_cycle')->nullable();
            $table->date('initial_certification_audit_completion_date')->nullable();

            // Certification cycle due dates
            $table->date('surveillance_1_due_date')->nullable();
            $table->date('surveillance_2_due_date')->nullable();
            $table->date('recertification_due_date')->nullable();
            $table->date('grace_period_end_date')->nullable();

            // Audit and certificate status fields
            $table->string('audit_status')->default('Not Scheduled');
            $table->string('certificate_status')->default('Active');

            // Audit team
            $table->string('lead_auditor')->nullable();
            $table->string('auditor_1')->nullable();
            $table->string('auditor_2')->nullable();
            $table->string('auditor_3')->nullable();
            $table->string('technical_expert')->nullable();

            // Workflow tracking
            $table->string('status')->default('Pending Review'); // Pending Review / Pending Approval / Approved
            $table->string('created_by')->default('System');
            $table->string('created_by_id')->nullable();
            $table->string('review_by')->nullable();
            $table->string('review_by_id')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('approval_by')->nullable();
            $table->string('approval_by_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('updated_by_id')->nullable();
            $table->string('deleted_by')->nullable();
            $table->string('deleted_by_id')->nullable();

            // Certificate PDF upload tracking
            $table->string('certificate_pdf')->nullable();
            $table->string('pdf_uploaded_by')->nullable();
            $table->string('pdf_uploaded_by_id')->nullable();
            $table->timestamp('pdf_uploaded_at')->nullable();

            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ba_client_id')->references('id')->on('ba_clients')->onDelete('restrict');
            $table->foreign('ba_standard_id')->references('id')->on('ba_standards')->onDelete('set null');
            $table->foreign('ba_accreditation_body_id')->references('id')->on('ba_accreditation_bodies')->onDelete('set null');

            $table->index('certificate_expiry_date');
            $table->index('surveillance_1_due_date');
            $table->index('surveillance_2_due_date');
            $table->index('recertification_due_date');
            $table->index('certificate_status');
            $table->index('audit_status');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ba_certificates');
    }
}
