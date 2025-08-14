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
            $table->string('status')->default('Approved')->after('expiry_date'); ///Default status "approved" while migration as previously approved certificates will be migrated. status flow: Pending Review-> Pending Approval ->Approved
            $table->string('created_by_id')->nullable()->after('created_by');
            $table->string('review_by')->nullable()->after('created_at'); ///User name in DB is collected in case user name is changed
            $table->string('review_by_id')->nullable()->after('review_by');     ///User id in DB is collected in case user name is changed
            $table->timestamp('reviewed_at')->nullable()->after('review_by_id');
            $table->string('approval_by')->nullable()->after('reviewed_at');
            $table->string('approval_by_id')->nullable()->after('approval_by'); ///User id in DB is collected in case user name is changed
            $table->timestamp('approved_at')->nullable()->after('approval_by_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->dropColumn(['status', 'created_by_id', 'review_by', 'review_by_id', 'reviewed_at', 'approval_by', 'approval_by_id', 'approved_at' ]);
        });
    }
}
