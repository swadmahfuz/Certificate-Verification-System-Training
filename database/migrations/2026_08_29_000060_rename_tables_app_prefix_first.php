<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameTablesAppPrefixFirst extends Migration
{
    public function up()
    {
        if (Schema::hasTable('certificates_training')) {
            Schema::table('certificates_training', function (Blueprint $table) {
                $table->dropForeign(['trainer_id']);
                $table->dropForeign(['signatory_id']);
            });
        }

        if (Schema::hasTable('ba_certificates')) {
            Schema::table('ba_certificates', function (Blueprint $table) {
                $table->dropForeign(['ba_client_id']);
                $table->dropForeign(['ba_standard_id']);
                $table->dropForeign(['ba_accreditation_body_id']);
            });
        }

        if (Schema::hasTable('ba_audit_reports')) {
            Schema::table('ba_audit_reports', function (Blueprint $table) {
                $table->dropForeign(['ba_certificate_id']);
            });
        }

        $renames = [
            'certificates_training_trainers' => 'training_trainers',
            'certificates_training_signatories' => 'training_signatories',
            'certificates_training' => 'training_certificates',
            'certificates_training_activity_logs' => 'training_activity_logs',
            'certificates_inspection' => 'inspection_certificates',
            'certificates_inspection_activity_logs' => 'inspection_activity_logs',
            'certificates_calibration' => 'calibration_certificates',
            'certificates_calibration_activity_logs' => 'calibration_activity_logs',
            'certificates_report' => 'reports_certificates',
            'certificates_report_activity_logs' => 'reports_activity_logs',
            'ba_standards' => 'certification_standards',
            'ba_accreditation_bodies' => 'certification_accreditation_bodies',
            'ba_clients' => 'certification_clients',
            'ba_certificates' => 'certification_certificates',
            'ba_audit_reports' => 'certification_audit_reports',
            'ba_activity_logs' => 'certification_activity_logs',
        ];

        foreach ($renames as $from => $to) {
            if (Schema::hasTable($from) && !Schema::hasTable($to)) {
                Schema::rename($from, $to);
            }
        }

        if (Schema::hasTable('training_certificates')) {
            Schema::table('training_certificates', function (Blueprint $table) {
                $table->foreign('trainer_id')
                    ->references('id')
                    ->on('training_trainers')
                    ->onUpdate('cascade')
                    ->onDelete('set null');

                $table->foreign('signatory_id')
                    ->references('id')
                    ->on('training_signatories')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            });
        }

        if (Schema::hasTable('certification_certificates')) {
            Schema::table('certification_certificates', function (Blueprint $table) {
                $table->foreign('ba_client_id')
                    ->references('id')
                    ->on('certification_clients')
                    ->onDelete('restrict');

                $table->foreign('ba_standard_id')
                    ->references('id')
                    ->on('certification_standards')
                    ->onDelete('set null');

                $table->foreign('ba_accreditation_body_id')
                    ->references('id')
                    ->on('certification_accreditation_bodies')
                    ->onDelete('set null');
            });
        }

        if (Schema::hasTable('certification_audit_reports')) {
            Schema::table('certification_audit_reports', function (Blueprint $table) {
                $table->foreign('ba_certificate_id')
                    ->references('id')
                    ->on('certification_certificates')
                    ->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('certification_audit_reports')) {
            Schema::table('certification_audit_reports', function (Blueprint $table) {
                $table->dropForeign(['ba_certificate_id']);
            });
        }

        if (Schema::hasTable('certification_certificates')) {
            Schema::table('certification_certificates', function (Blueprint $table) {
                $table->dropForeign(['ba_client_id']);
                $table->dropForeign(['ba_standard_id']);
                $table->dropForeign(['ba_accreditation_body_id']);
            });
        }

        if (Schema::hasTable('training_certificates')) {
            Schema::table('training_certificates', function (Blueprint $table) {
                $table->dropForeign(['trainer_id']);
                $table->dropForeign(['signatory_id']);
            });
        }

        $renames = [
            'certification_activity_logs' => 'ba_activity_logs',
            'certification_audit_reports' => 'ba_audit_reports',
            'certification_certificates' => 'ba_certificates',
            'certification_clients' => 'ba_clients',
            'certification_accreditation_bodies' => 'ba_accreditation_bodies',
            'certification_standards' => 'ba_standards',
            'reports_activity_logs' => 'certificates_report_activity_logs',
            'reports_certificates' => 'certificates_report',
            'calibration_activity_logs' => 'certificates_calibration_activity_logs',
            'calibration_certificates' => 'certificates_calibration',
            'inspection_activity_logs' => 'certificates_inspection_activity_logs',
            'inspection_certificates' => 'certificates_inspection',
            'training_activity_logs' => 'certificates_training_activity_logs',
            'training_certificates' => 'certificates_training',
            'training_signatories' => 'certificates_training_signatories',
            'training_trainers' => 'certificates_training_trainers',
        ];

        foreach ($renames as $from => $to) {
            if (Schema::hasTable($from) && !Schema::hasTable($to)) {
                Schema::rename($from, $to);
            }
        }

        if (Schema::hasTable('certificates_training')) {
            Schema::table('certificates_training', function (Blueprint $table) {
                $table->foreign('trainer_id')
                    ->references('id')
                    ->on('certificates_training_trainers')
                    ->onUpdate('cascade')
                    ->onDelete('set null');

                $table->foreign('signatory_id')
                    ->references('id')
                    ->on('certificates_training_signatories')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
            });
        }

        if (Schema::hasTable('ba_certificates')) {
            Schema::table('ba_certificates', function (Blueprint $table) {
                $table->foreign('ba_client_id')->references('id')->on('ba_clients')->onDelete('restrict');
                $table->foreign('ba_standard_id')->references('id')->on('ba_standards')->onDelete('set null');
                $table->foreign('ba_accreditation_body_id')->references('id')->on('ba_accreditation_bodies')->onDelete('set null');
            });
        }

        if (Schema::hasTable('ba_audit_reports')) {
            Schema::table('ba_audit_reports', function (Blueprint $table) {
                $table->foreign('ba_certificate_id')->references('id')->on('ba_certificates')->onDelete('cascade');
            });
        }
    }
}
