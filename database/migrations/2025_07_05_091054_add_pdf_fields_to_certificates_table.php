<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPdfFieldsToCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('certificates', function (Blueprint $table) {
            $table->string('certificate_pdf')->nullable()->after('deleted_at'); ///PDF file path for the certificate, nullable to allow existing records without a PDF
            $table->string('pdf_uploaded_by')->nullable()->after('certificate_pdf'); ///User name in DB is collected in case user name is changed
            $table->string('pdf_uploaded_by_id')->nullable()->after('pdf_uploaded_by'); ///User id in DB is collected in case user name is changed
            $table->timestamp('pdf_uploaded_at')->nullable()->after('pdf_uploaded_by_id'); ///Timestamp for when the PDF was uploaded    
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
            $table->dropColumn([
                'certificate_pdf',
                'pdf_uploaded_by',
                'pdf_uploaded_by_id',
                'pdf_uploaded_at'
            ]);
        });
    }
}
