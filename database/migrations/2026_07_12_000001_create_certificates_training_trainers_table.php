<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTrainingTrainersTable extends Migration
{
    /**
     * Create the Training CVS trainers table.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates_training_trainers', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('designation');

            // Stored in Laravel private storage.
            $table->string('signature_path')->nullable();

            // Inactive trainers remain available for historical certificates.
            $table->boolean('is_active')->default(true);

            // User who originally added the trainer.
            $table->unsignedBigInteger('created_by_id')->nullable();

            $table->timestamps();

            $table->index('is_active');
            $table->index('created_by_id');
        });
    }

    /**
     * Remove the Training CVS trainers table.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificates_training_trainers');
    }
}