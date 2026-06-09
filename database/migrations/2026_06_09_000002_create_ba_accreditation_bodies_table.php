<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaAccreditationBodiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ba_accreditation_bodies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('accreditation_body_name')->unique(); // Example: ESYD, EGAC, DAkkS, UKAS
            $table->string('short_name')->nullable();
            $table->string('country')->nullable();
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
        Schema::dropIfExists('ba_accreditation_bodies');
    }
}
