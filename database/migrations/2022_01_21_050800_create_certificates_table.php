<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('certificate_number')->unique();
            $table->string('participant_name');
            $table->string('passport_nid');
            $table->string('driving_license')->nullable();
            $table->string('company')->nullable();
            $table->string('training_name');
            $table->string('location');
            $table->string('trainer');
            $table->string('training_date');
            $table->string('training_end');
            $table->string('issue_date');
            $table->string('expiry_date')->nullable();
            $table->string('created_by')->default('Bulk uploaded');
            ///Only use below lines while creating the table for the first time. ///
            // $table->string('created_by_id')->nullable();
            // $table->string('review_by')->nullable()->after('created_by_id'); ///User name in DB is collected in case user name is changed
            // $table->string('review_by_id')->nullable()->after('review_by');     ///User id in DB is collected in case user name is changed
            // $table->string('approval_by')->nullable()->after('review_by_id');
            // $table->string('approval_by_id')->nullable()->after('approval_by'); ///User id in DB is collected in case user name is changed
            // $table->string('status')->default('Approved')->after('approval_by_id'); ///Default status "approved" while migration as previously approved certificates will be migrated. status flow: Pending Review-> Pending Approval ->Approved
            // $table->timestamp('reviewed_at')->nullable()->after('created_at');
            // $table->timestamp('approved_at')->nullable()->after('reviewed_at');
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes(); ///create 'deleted at' column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('certificates');
    }
}
