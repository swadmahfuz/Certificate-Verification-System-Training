<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameCertificationColumns extends Migration
{
    private function foreignKeyExists(string $table, string $column): bool
    {
        $result = DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?
             AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$table, $column]
        );

        return count($result) > 0;
    }

    private function dropForeignKeyIfExists(string $table, string $column): void
    {
        if (!$this->foreignKeyExists($table, $column)) {
            return;
        }

        Schema::table($table, function (Blueprint $blueprint) use ($column) {
            $blueprint->dropForeign([$column]);
        });
    }

    public function up()
    {
        if (!Schema::hasTable('certification_certificates')) {
            return;
        }

        if (Schema::hasColumn('certification_certificates', 'ba_client_id')) {
            $this->dropForeignKeyIfExists('certification_certificates', 'ba_client_id');
            $this->dropForeignKeyIfExists('certification_certificates', 'ba_standard_id');
            $this->dropForeignKeyIfExists('certification_certificates', 'ba_accreditation_body_id');

            DB::statement('ALTER TABLE certification_certificates CHANGE ba_client_id certification_client_id INT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE certification_certificates CHANGE ba_standard_id certification_standard_id INT UNSIGNED NULL');
            DB::statement('ALTER TABLE certification_certificates CHANGE ba_accreditation_body_id certification_accreditation_body_id INT UNSIGNED NULL');
        }

        if (Schema::hasColumn('certification_certificates', 'certification_client_id')) {
            $this->dropForeignKeyIfExists('certification_certificates', 'certification_client_id');
            $this->dropForeignKeyIfExists('certification_certificates', 'certification_standard_id');
            $this->dropForeignKeyIfExists('certification_certificates', 'certification_accreditation_body_id');

            Schema::table('certification_certificates', function (Blueprint $table) {
                $table->foreign('certification_client_id', 'cert_certs_client_id_fk')
                    ->references('id')
                    ->on('certification_clients')
                    ->onDelete('restrict');

                $table->foreign('certification_standard_id', 'cert_certs_standard_id_fk')
                    ->references('id')
                    ->on('certification_standards')
                    ->onDelete('set null');

                $table->foreign('certification_accreditation_body_id', 'cert_certs_acc_body_id_fk')
                    ->references('id')
                    ->on('certification_accreditation_bodies')
                    ->onDelete('set null');
            });
        }

        if (!Schema::hasTable('certification_audit_reports')) {
            return;
        }

        if (Schema::hasColumn('certification_audit_reports', 'ba_certificate_id')) {
            $this->dropForeignKeyIfExists('certification_audit_reports', 'ba_certificate_id');

            DB::statement('ALTER TABLE certification_audit_reports CHANGE ba_certificate_id certification_certificate_id INT UNSIGNED NOT NULL');
        }

        if (Schema::hasColumn('certification_audit_reports', 'certification_certificate_id')) {
            $this->dropForeignKeyIfExists('certification_audit_reports', 'certification_certificate_id');

            Schema::table('certification_audit_reports', function (Blueprint $table) {
                $table->foreign('certification_certificate_id', 'cert_audit_reports_cert_id_fk')
                    ->references('id')
                    ->on('certification_certificates')
                    ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('certification_audit_reports') && Schema::hasColumn('certification_audit_reports', 'certification_certificate_id')) {
            $this->dropForeignKeyIfExists('certification_audit_reports', 'certification_certificate_id');

            DB::statement('ALTER TABLE certification_audit_reports CHANGE certification_certificate_id ba_certificate_id INT UNSIGNED NOT NULL');

            Schema::table('certification_audit_reports', function (Blueprint $table) {
                $table->foreign('ba_certificate_id', 'cert_audit_reports_ba_cert_id_fk')
                    ->references('id')
                    ->on('certification_certificates')
                    ->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('certification_certificates') || !Schema::hasColumn('certification_certificates', 'certification_client_id')) {
            return;
        }

        $this->dropForeignKeyIfExists('certification_certificates', 'certification_client_id');
        $this->dropForeignKeyIfExists('certification_certificates', 'certification_standard_id');
        $this->dropForeignKeyIfExists('certification_certificates', 'certification_accreditation_body_id');

        DB::statement('ALTER TABLE certification_certificates CHANGE certification_client_id ba_client_id INT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE certification_certificates CHANGE certification_standard_id ba_standard_id INT UNSIGNED NULL');
        DB::statement('ALTER TABLE certification_certificates CHANGE certification_accreditation_body_id ba_accreditation_body_id INT UNSIGNED NULL');

        Schema::table('certification_certificates', function (Blueprint $table) {
            $table->foreign('ba_client_id', 'cert_certs_ba_client_id_fk')
                ->references('id')
                ->on('certification_clients')
                ->onDelete('restrict');

            $table->foreign('ba_standard_id', 'cert_certs_ba_standard_id_fk')
                ->references('id')
                ->on('certification_standards')
                ->onDelete('set null');

            $table->foreign('ba_accreditation_body_id', 'cert_certs_ba_acc_body_id_fk')
                ->references('id')
                ->on('certification_accreditation_bodies')
                ->onDelete('set null');
        });
    }
}
