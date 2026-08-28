<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificationCertificatesTable extends Migration
{
    public function up()
    {
        Schema::create('certification_certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('certification_client_id');
            $table->unsignedInteger('certification_standard_id')->nullable();
            $table->unsignedInteger('certification_accreditation_body_id')->nullable();
            $table->string('certificate_number')->nullable()->unique();
            $table->text('certificate_scope')->nullable();
            $table->date('certificate_issue_date')->nullable();
            $table->date('certificate_expiry_date')->nullable();
            $table->string('certification_cycle')->nullable();
            $table->date('initial_certification_audit_completion_date')->nullable();
            $table->date('surveillance_1_due_date')->nullable();
            $table->date('surveillance_2_due_date')->nullable();
            $table->date('recertification_due_date')->nullable();
            $table->date('grace_period_end_date')->nullable();
            $table->string('audit_status')->default('Not Scheduled');
            $table->string('certificate_status')->default('Active');
            $table->string('lead_auditor')->nullable();
            $table->string('auditor_1')->nullable();
            $table->string('auditor_2')->nullable();
            $table->string('auditor_3')->nullable();
            $table->string('technical_expert')->nullable();
            $table->string('status')->default('Pending Review');
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
            $table->string('certificate_pdf')->nullable();
            $table->string('pdf_uploaded_by')->nullable();
            $table->string('pdf_uploaded_by_id')->nullable();
            $table->timestamp('pdf_uploaded_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('certification_client_id', 'cert_certs_client_id_fk')->references('id')->on('certification_clients')->onDelete('restrict');
            $table->foreign('certification_standard_id', 'cert_certs_standard_id_fk')->references('id')->on('certification_standards')->onDelete('set null');
            $table->foreign('certification_accreditation_body_id', 'cert_certs_acc_body_id_fk')->references('id')->on('certification_accreditation_bodies')->onDelete('set null');

            $table->index('certificate_expiry_date');
            $table->index('surveillance_1_due_date');
            $table->index('surveillance_2_due_date');
            $table->index('recertification_due_date');
            $table->index('certificate_status');
            $table->index('audit_status');
            $table->index('status');
            $table->index('approved_at', 'certification_certificates_approved_at_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('certification_certificates');
    }
}
