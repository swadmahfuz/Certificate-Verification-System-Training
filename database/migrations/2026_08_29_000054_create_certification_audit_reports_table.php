<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificationAuditReportsTable extends Migration
{
    public function up()
    {
        Schema::create('certification_audit_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('certification_certificate_id');
            $table->string('audit_year')->nullable();
            $table->string('audit_type');
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

            $table->foreign('certification_certificate_id', 'cert_audit_reports_cert_id_fk')->references('id')->on('certification_certificates')->onDelete('cascade');

            $table->index('certification_certificate_id');
            $table->index('audit_type');
            $table->index('audit_date');
            $table->index('audit_year');
        });
    }

    public function down()
    {
        Schema::dropIfExists('certification_audit_reports');
    }
}
