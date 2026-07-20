<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInternalAuditTrainingToCertificatesTrainingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('certificates_training', function (Blueprint $table) {
            $table->boolean('internal_audit_training')
                ->default(false)
                ->after('has_practical');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('certificates_training', function (Blueprint $table) {
            $table->dropColumn('internal_audit_training');
        });
    }
}