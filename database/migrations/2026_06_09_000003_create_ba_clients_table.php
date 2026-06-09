<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ba_clients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('client_name');
            $table->text('client_address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('remarks')->nullable();
            $table->string('created_by')->default('System');
            $table->string('created_by_id')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('updated_by_id')->nullable();
            $table->string('deleted_by')->nullable();
            $table->string('deleted_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('client_name');
            $table->index('email');
            $table->index('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ba_clients');
    }
}
