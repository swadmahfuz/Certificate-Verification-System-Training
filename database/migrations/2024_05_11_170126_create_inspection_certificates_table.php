<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inspection_certificates', function (Blueprint $table) {
            // 👉 Un-comment the following lines ONLY when setting up a fresh database
            $table->increments('id');
            $table->string('certificate_number')->unique();
            // Core inspection-specific fields
            $table->string('inspector');
            $table->string('client_name');
            $table->string('inspection_type');
            $table->text('inspection_location');
            $table->string('equipment_name');
            $table->string('equipment_brand')->nullable();
            $table->string('equipment_serial_chassis')->nullable();
            $table->string('equipment_rated_capacity')->nullable();
            $table->string('equipment_swl')->nullable();
            $table->string('inspection_date');
            $table->string('validity_date')->nullable();
            $table->text('inspection_remarks')->nullable();
            $table->text('inspection_internal_notes')->nullable();
            // $table->string('status')->default('Approved'); /// Default for migrated data
            $table->string('created_by')->default('Bulk uploaded');
            // $table->string('created_by_id')->nullable();
            $table->timestamp('created_at');
            // $table->string('review_by')->nullable(); /// User name preserved even if account name changes
            // $table->string('review_by_id')->nullable(); /// User ID preserved
            // $table->timestamp('reviewed_at')->nullable();
            // $table->string('approval_by')->nullable();
            // $table->string('approval_by_id')->nullable();
            // $table->timestamp('approved_at')->nullable();
            $table->string('updated_by')->nullable();
            //$table->string('updated_by_id')->nullable();
            $table->timestamp('updated_at');
            $table->string('deleted_by')->nullable();
            //$table->string('deleted_by_id')->nullable();
            $table->softDeletes(); // Creates 'deleted_at' column
            // $table->string('certificate_pdf')->nullable(); /// File path of uploaded PDF
            // $table->string('pdf_uploaded_by')->nullable(); /// Name of user who uploaded PDF
            // $table->string('pdf_uploaded_by_id')->nullable(); /// ID of user who uploaded PDF
            // $table->timestamp('pdf_uploaded_at')->nullable(); /// Timestamp of PDF upload
            
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