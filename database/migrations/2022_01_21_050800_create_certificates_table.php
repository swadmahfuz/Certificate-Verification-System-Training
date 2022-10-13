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
            $table->string('certificate_number');
            $table->string('participant_name');
            $table->string('company');
            $table->string('training_name');
            $table->string('trainer');
            $table->string('training_date');
            $table->string('issue_date');
            $table->string('expiry_date');
            $table->string('passport_nid');
            $table->string('driving_license');
            $table->timestamps();
            //Training Name, Name, Passport/NID, DL, Company, Training Date, 
            //Issue Date, Expiry Date, Trainer
            /*
            $table->string('certificate_id');
            $table->string('st_name');
            $table->string('st_id');
            $table->string('course_code');
            $table->string('course_result');
            */
            

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
