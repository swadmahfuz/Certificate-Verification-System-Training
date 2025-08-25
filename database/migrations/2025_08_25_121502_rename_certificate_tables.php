<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameCertificateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('certificates', 'certificates_training');
        Schema::rename('inspection_certificates', 'certificates_inspection');
        Schema::rename('calibration_certificates', 'certificates_calibration');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('certificates_training', 'certificates');
        Schema::rename('certificates_inspection', 'inspection_certificates');
        Schema::rename('certificates_calibration', 'calibration_certificates');
    }
}