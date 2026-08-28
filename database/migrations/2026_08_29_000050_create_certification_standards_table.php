<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificationStandardsTable extends Migration
{
    public function up()
    {
        Schema::create('certification_standards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('standard_name')->unique();
            $table->string('standard_code')->nullable();
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
        Schema::dropIfExists('certification_standards');
    }
}
