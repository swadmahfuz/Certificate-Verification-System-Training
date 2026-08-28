<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingSignatoriesTable extends Migration
{
    public function up()
    {
        Schema::create('training_signatories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('designation');
            $table->string('department')->nullable();
            $table->string('signature_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('created_by_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('training_signatories');
    }
}
