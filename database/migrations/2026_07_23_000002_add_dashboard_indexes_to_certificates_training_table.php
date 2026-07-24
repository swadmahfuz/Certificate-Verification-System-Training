<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDashboardIndexesToCertificatesTrainingTable extends Migration
{
    public function up()
    {
        Schema::table('certificates_training', function (Blueprint $table) {
            $table->index('status', 'certificates_training_status_index');
            $table->index('approved_at', 'certificates_training_approved_at_index');
            $table->index('issue_date', 'certificates_training_issue_date_index');
            $table->index('expiry_date', 'certificates_training_expiry_date_index');
        });
    }

    public function down()
    {
        Schema::table('certificates_training', function (Blueprint $table) {
            $table->dropIndex('certificates_training_status_index');
            $table->dropIndex('certificates_training_approved_at_index');
            $table->dropIndex('certificates_training_issue_date_index');
            $table->dropIndex('certificates_training_expiry_date_index');
        });
    }
}
