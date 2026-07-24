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
        ]);
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('certificates_training_activity_logs', function (Blueprint $table) {
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

        $this->assertDatabaseHas('certificates_training_activity_logs', [
            'event' => 'certificate.approved',
            'subject_type' => 'certificate',
            'subject_id' => 17,
            'causer_id' => 42,
            'causer_name' => 'Audit User',
        ]);
    }
}
