<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ActivityLogServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'cvs.app_key' => 'training',
            'cvs.apps' => [
                'training' => 'Training CVS',
                'inspection' => 'Inspection CVS',
                'calibration' => 'Calibration CVS',
                'reports' => 'Reports CVS',
                'certification' => 'BA Certification',
            ],
            'cvs.shared_activity_subject_types' => ['auth', 'user', 'department'],
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        foreach (array_keys(config('cvs.apps')) as $appKey) {
            Schema::create($appKey . '_activity_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('event');
                $table->string('subject_type');
                $table->unsignedInteger('subject_id')->nullable();
                $table->unsignedInteger('causer_id')->nullable();
                $table->string('causer_name')->nullable();
                $table->string('description');
                $table->text('properties')->nullable();
                $table->string('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('created_at');
            });
        }
    }

    public function test_activity_service_records_actor_and_subject()
    {
        $user = new User(['name' => 'Audit User', 'email' => 'audit@example.com']);
        $user->id = 42;
        $this->actingAs($user);

        app(ActivityLogService::class)->record(
            'certificate.approved',
            'certificate',
            17,
            'Certificate TR-017 was approved.',
            ['status' => 'Approved']
        );

        $this->assertDatabaseHas('training_activity_logs', [
            'event' => 'certificate.approved',
            'subject_type' => 'certificate',
            'subject_id' => 17,
            'causer_id' => 42,
            'causer_name' => 'Audit User',
        ]);

        foreach (['inspection', 'calibration', 'reports', 'certification'] as $appKey) {
            $this->assertDatabaseCount($appKey . '_activity_logs', 0);
        }
    }

    public function test_shared_user_events_fan_out_to_all_app_tables()
    {
        $user = new User(['name' => 'Admin User', 'email' => 'admin@example.com']);
        $user->id = 1;
        $this->actingAs($user);

        app(ActivityLogService::class)->record(
            'user.created',
            'user',
            99,
            'User "New User" was created by Admin User.'
        );

        foreach (array_keys(config('cvs.apps')) as $appKey) {
            $this->assertDatabaseHas($appKey . '_activity_logs', [
                'event' => 'user.created',
                'subject_type' => 'user',
                'subject_id' => 99,
                'causer_id' => 1,
            ]);

            $row = DB::table($appKey . '_activity_logs')->first();
            $properties = json_decode($row->properties, true);
            $this->assertSame('training', $properties['source_app']);
        }
    }

    public function test_shared_department_events_fan_out_to_all_app_tables()
    {
        app(ActivityLogService::class)->record(
            'department.created',
            'department',
            5,
            'Department "QA" was created.'
        );

        foreach (array_keys(config('cvs.apps')) as $appKey) {
            $this->assertDatabaseHas($appKey . '_activity_logs', [
                'event' => 'department.created',
                'subject_type' => 'department',
                'subject_id' => 5,
            ]);
        }
    }

    public function test_app_local_export_events_stay_in_current_app_table_only()
    {
        app(ActivityLogService::class)->record(
            'export.completed',
            'export',
            null,
            'Certificate export completed.'
        );

        $this->assertDatabaseCount('training_activity_logs', 1);
        $this->assertDatabaseHas('training_activity_logs', [
            'event' => 'export.completed',
            'subject_type' => 'export',
        ]);

        foreach (['inspection', 'calibration', 'reports', 'certification'] as $appKey) {
            $this->assertDatabaseCount($appKey . '_activity_logs', 0);
        }
    }
}
