<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBaAuditReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ba_audit_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ba_certificate_id');
            $table->string('audit_year')->nullable(); // Example: 2026
            $table->string('audit_type'); // Initial / Surveillance 1 / Surveillance 2 / Recertification / Special / Follow-up
            $table->date('audit_date')->nullable();
            $table->string('audit_report_file')->nullable();
            $table->text('remarks')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->string('uploaded_by_id')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->string('created_by')->nullable();
            $table->string('created_by_id')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('updated_by_id')->nullable();
            $table->string('deleted_by')->nullable();
            $table->string('deleted_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('ba_certificate_id')->references('id')->on('ba_certificates')->onDelete('cascade');

            $table->index('ba_certificate_id');
            $table->index('audit_type');
            $table->index('audit_date');
            $table->index('audit_year');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ba_audit_reports');
    }
}
