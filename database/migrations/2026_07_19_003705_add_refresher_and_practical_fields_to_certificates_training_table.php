<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRefresherAndPracticalFieldsToCertificatesTrainingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('certificates_training', function (Blueprint $table) {
            $table->boolean('is_refresher')
                ->default(false)
                ->after('certificate_type');

            $table->boolean('has_practical')
                ->default(false)
                ->after('is_refresher');
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
            $table->dropColumn([
                'is_refresher',
                'has_practical',
            ]);
        });
    }
}