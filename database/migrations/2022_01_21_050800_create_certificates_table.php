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
            $table->string('issue_date');
            $table->string('expiry_date');
            $table->string('file')->nullable();
            $table->string('created_by')->default('Bulk uploaded');
            $table->string('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes(); ///create 'deleted at column'
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
