<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('certificate_number')->unique();
            $table->string('participant_name');
            $table->string('passport_nid');
            $table->string('driving_license')->nullable();
            $table->string('company')->nullable();
            $table->string('training_name');
            $table->string('location');
            $table->string('trainer');
            $table->string('training_date');
            $table->string('training_end');
            $table->string('issue_date');
            $table->string('expiry_date')->nullable();
            $table->string('created_by')->default('Bulk uploaded');

            // 👉 Un-comment the following lines ONLY when setting up a fresh database:
            // $table->string('created_by_id')->nullable();
            // $table->string('review_by')->nullable(); /// User name preserved even if account name changes
            // $table->string('review_by_id')->nullable(); /// User ID preserved
            // $table->string('approval_by')->nullable();
            // $table->string('approval_by_id')->nullable();
            // $table->string('status')->default('Approved'); /// Default for migrated data
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            // $table->timestamp('reviewed_at')->nullable()->after('created_at');
            // $table->timestamp('approved_at')->nullable()->after('reviewed_at');

            // --- PDF Upload ---
            // $table->string('certificate_pdf')->nullable(); /// File path of uploaded PDF
            // $table->string('pdf_uploaded_by')->nullable(); /// Name of user who uploaded PDF
            // $table->string('pdf_uploaded_by_id')->nullable(); /// ID of user who uploaded PDF
            // $table->timestamp('pdf_uploaded_at')->nullable(); /// Timestamp of PDF upload

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
        Schema::dropIfExists('certificates');
    }
}
