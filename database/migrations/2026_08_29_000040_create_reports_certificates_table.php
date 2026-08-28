<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsCertificatesTable extends Migration
{
    public function up()
    {
        Schema::create('reports_certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('certificate_number')->unique();
            $table->string('client_name');
            $table->text('location')->nullable();
            $table->string('team_members')->nullable();
            $table->string('report_prepared_by');
            $table->string('report_approved_by');
            $table->string('report_issue_date');
            $table->string('report_validity_date')->nullable();
            $table->string('report_revision')->nullable();
            $table->text('report_remarks')->nullable();
            $table->text('report_internal_notes')->nullable();
            $table->string('status')->default('Approved');
            $table->string('created_by')->default('Bulk uploaded');
            $table->string('created_by_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('review_by')->nullable();
            $table->string('review_by_id')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('approval_by')->nullable();
            $table->string('approval_by_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('updated_by_id')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by')->nullable();
            $table->string('deleted_by_id')->nullable();
            $table->softDeletes();
            $table->string('certificate_pdf')->nullable();
            $table->string('pdf_uploaded_by')->nullable();
            $table->string('pdf_uploaded_by_id')->nullable();
            $table->timestamp('pdf_uploaded_at')->nullable();

            $table->index('status', 'reports_certificates_status_index');
            $table->index('approved_at', 'reports_certificates_approved_at_index');
            $table->index('report_issue_date', 'reports_certificates_report_issue_date_index');
            $table->index('report_validity_date', 'reports_certificates_report_validity_date_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports_certificates');
    }
}
