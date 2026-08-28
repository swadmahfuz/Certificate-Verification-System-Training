<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInspectionCertificatesTable extends Migration
{
    public function up()
    {
        Schema::create('inspection_certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('certificate_number')->unique();
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

            $table->index('status', 'inspection_certificates_status_index');
            $table->index('approved_at', 'inspection_certificates_approved_at_index');
            $table->index('inspection_date', 'inspection_certificates_inspection_date_index');
            $table->index('validity_date', 'inspection_certificates_validity_date_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inspection_certificates');
    }
}
