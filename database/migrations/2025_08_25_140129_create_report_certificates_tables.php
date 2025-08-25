<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsCertificatesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates_report', function (Blueprint $table) {
            $table->increments('id');
            $table->string('certificate_number')->unique();
            $table->string('client_name');
            $table->text('location')->nullable();
            $table->string('team_members')->nullable();
            $table->string('report_prepared_by');
            $table->string('report_approved_by'); ///Different from 'approval_by' which is the user who approved the certificate in the system
            $table->string('report_issue_date');
            $table->string('report_validity_date')->nullable();
            $table->string('report_revision')->nullable();
            $table->text('report_remarks')->nullable();
            $table->text('report_internal_notes')->nullable();
            $table->string('status')->default('Approved'); /// Default for migrated data
            $table->string('created_by')->default('Bulk uploaded');
            $table->string('created_by_id')->nullable();
            $table->timestamp('created_at');
            $table->string('review_by')->nullable(); /// User name preserved even if account name changes
            $table->string('review_by_id')->nullable(); /// User ID preserved
            $table->timestamp('reviewed_at')->nullable();
            $table->string('approval_by')->nullable();
            $table->string('approval_by_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('updated_by_id')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('certificate_pdf')->nullable(); /// File path of uploaded PDF
            $table->string('pdf_uploaded_by')->nullable(); /// Name of user who uploaded PDF
            $table->string('pdf_uploaded_by_id')->nullable(); /// ID of user who uploaded PDF
            $table->timestamp('pdf_uploaded_at')->nullable(); /// Timestamp of PDF upload
            $table->string('deleted_by')->nullable();
            $table->timestamp('deleted_by_id')->nullable(); 
            $table->softDeletes(); // Creates 'deleted_at' column
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inspection_certificates');
    }
}
