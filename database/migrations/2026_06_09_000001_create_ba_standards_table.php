<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaStandardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ba_standards', function (Blueprint $table) {
            $table->increments('id');
            $table->string('standard_name')->unique(); // Example: ISO 9001:2015
            $table->string('standard_code')->nullable(); // Example: ISO 9001
            $table->string('status')->default('Active'); // Active / Inactive
            $table->string('created_by')->nullable();
            $table->string('created_by_id')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('updated_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ba_standards');
    }
}
