<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviewedAndApprovedToCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('created_by_id')->nullable()->after('created_by');
            $table->string('review_by')->nullable()->after('created_by_id'); ///User name in DB is collected in case user name is changed
            $table->string('review_by_id')->nullable()->after('review_by');     ///User id in DB is collected in case user name is changed
            $table->string('approval_by')->nullable()->after('review_by_id');
            $table->string('approval_by_id')->nullable()->after('approval_by'); ///User id in DB is collected in case user name is changed
            $table->string('status')->default('Approved')->after('approval_by_id'); ///Default status "approved" while migration as previously approved certificates will be migrated. status flow: Pending Review-> Pending Approval ->Approved
            $table->timestamp('reviewed_at')->nullable()->after('created_at');
            $table->timestamp('approved_at')->nullable()->after('reviewed_at');
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
            $table->dropColumn(['review_by', 'review_by_id', 'approval_by', 'approval_by_id', 'status', 'reviewed_at', 'approved_at']);
        });
    }
}
