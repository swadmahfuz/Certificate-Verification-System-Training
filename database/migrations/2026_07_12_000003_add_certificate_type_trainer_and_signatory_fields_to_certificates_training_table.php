<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCertificateTypeTrainerAndSignatoryFieldsToCertificatesTrainingTable extends Migration
{
    /**
     * Add certificate type, trainer and optional signatory fields.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('certificates_training', function (Blueprint $table) {
            $table->string('certificate_type')->nullable()->after('certificate_number');
            /// Trainer relationship and historical snapshot fields
            $table->unsignedBigInteger('trainer_id')->nullable()->after('trainer');
            $table->string('trainer_email')->nullable()->after('trainer_id');
            $table->string('trainer_designation')->nullable()->after('trainer_email');
            $table->string('trainer_signature_path')->nullable()->after('trainer_designation');
            /// Optional signatory relationship and historical snapshot fields
            $table->unsignedBigInteger('signatory_id')->nullable()->after('trainer_signature_path');
            $table->string('signatory_name')->nullable()->after('signatory_id');
            $table->string('signatory_email')->nullable()->after('signatory_name');
            $table->string('signatory_designation')->nullable()->after('signatory_email');
            $table->string('signatory_department')->nullable()->after('signatory_designation');
            $table->string('signatory_signature_path')->nullable()->after('signatory_department');
            
            $table->index('certificate_type');
            $table->index('trainer_id');
            $table->index('signatory_id');
            
            $table->foreign('trainer_id')
                ->references('id')
                ->on('certificates_training_trainers')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('signatory_id')
                ->references('id')
                ->on('certificates_training_signatories')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    /**
     * Remove certificate type, trainer and signatory fields.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('certificates_training', function (Blueprint $table) {
            $table->dropForeign(['trainer_id']);
            $table->dropForeign(['signatory_id']);

            $table->dropIndex(['certificate_type']);
            $table->dropIndex(['trainer_id']);
            $table->dropIndex(['signatory_id']);

            $table->dropColumn([
                'certificate_type',
                'trainer_id',
                'trainer_email',
                'trainer_designation',
                'trainer_signature_path',
                'signatory_id',
                'signatory_name',
                'signatory_email',
                'signatory_designation',
                'signatory_department',
                'signatory_signature_path',
            ]);
        });
    }
}