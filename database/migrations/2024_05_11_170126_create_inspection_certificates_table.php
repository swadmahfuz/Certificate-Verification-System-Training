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
            $table->string('created_by')->default('Bulk uploaded');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes(); ///create 'deleted at' column
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
