<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviewApprovalPdfToInspectionCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inspection_certificates', function (Blueprint $table) {

            // Status (for migrated or workflow-driven data)
            $table->string('status')->default('Approved')->after('inspection_internal_notes');
            $table->string('created_by_id')->nullable()->after('created_by');
            
            // Review & approval
            $table->string('review_by')->nullable()->after('created_at');
            $table->string('review_by_id')->nullable()->after('review_by');
            $table->timestamp('reviewed_at')->nullable()->after('review_by_id');

            $table->string('approval_by')->nullable()->after('reviewed_at');
            $table->string('approval_by_id')->nullable()->after('approval_by');
            $table->timestamp('approved_at')->nullable()->after('approval_by_id');
            $table->string('updated_by_id')->nullable()->after('updated_by');
            $table->string('deleted_by_id')->nullable()->after('deleted_by');

            // PDF Upload
            $table->string('certificate_pdf')->nullable()->after('deleted_at');
            $table->string('pdf_uploaded_by')->nullable()->after('certificate_pdf');
            $table->string('pdf_uploaded_by_id')->nullable()->after('pdf_uploaded_by');
            $table->timestamp('pdf_uploaded_at')->nullable()->after('pdf_uploaded_by_id');
            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inspection_certificates', function (Blueprint $table) {
            $table->dropColumn([
                'status', 'created_by_id',
                'review_by', 'review_by_id', 'reviewed_at',
                'approval_by', 'approval_by_id', 'approved_at',
                'updated_by_id', 'deleted_by_id',
                'certificate_pdf', 'pdf_uploaded_by', 'pdf_uploaded_by_id', 'pdf_uploaded_at',
            ]);
        });
    }
}
