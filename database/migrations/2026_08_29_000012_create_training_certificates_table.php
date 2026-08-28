<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingCertificatesTable extends Migration
{
    public function up()
    {
        Schema::create('training_certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('certificate_number')->unique();
            $table->string('certificate_type')->nullable();
            $table->boolean('is_refresher')->default(false);
            $table->boolean('has_practical')->default(false);
            $table->boolean('internal_audit_training')->default(false);
            $table->boolean('online_training')->default(false);
            $table->string('participant_name');
            $table->string('passport_nid');
            $table->string('driving_license')->nullable();
            $table->string('company')->nullable();
            $table->string('training_name');
            $table->string('location');
            $table->string('trainer');
            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->string('trainer_email')->nullable();
            $table->string('trainer_designation')->nullable();
            $table->string('trainer_signature_path')->nullable();
            $table->unsignedBigInteger('signatory_id')->nullable();
            $table->string('signatory_name')->nullable();
            $table->string('signatory_email')->nullable();
            $table->string('signatory_designation')->nullable();
            $table->string('signatory_department')->nullable();
            $table->string('signatory_signature_path')->nullable();
            $table->string('training_date');
            $table->string('training_end');
            $table->string('issue_date');
            $table->string('expiry_date')->nullable();
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

            $table->index('certificate_type');
            $table->index('trainer_id');
            $table->index('signatory_id');
            $table->index('status', 'training_certificates_status_index');
            $table->index('approved_at', 'training_certificates_approved_at_index');
            $table->index('issue_date', 'training_certificates_issue_date_index');
            $table->index('expiry_date', 'training_certificates_expiry_date_index');

            $table->foreign('trainer_id')
                ->references('id')
                ->on('training_trainers')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('signatory_id')
                ->references('id')
                ->on('training_signatories')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_certificates');
    }
}
