<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificationAccreditationBodiesTable extends Migration
{
    public function up()
    {
        Schema::create('certification_accreditation_bodies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('accreditation_body_name')->unique();
            $table->string('short_name')->nullable();
            $table->string('country')->nullable();
            $table->string('status')->default('Active');
            $table->string('created_by')->nullable();
            $table->string('created_by_id')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('updated_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('certification_accreditation_bodies');
    }
}
