<?php

namespace Tests\Feature;

use App\Services\DashboardService;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DashboardServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('training_certificates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('certificate_number')->unique();
            $table->string('participant_name');
            $table->string('status');
            $table->string('issue_date')->nullable();
            $table->string('expiry_date')->nullable();
            $table->unsignedInteger('review_by_id')->nullable();
            $table->unsignedInteger('approval_by_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('training_trainers', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function test_dashboard_aggregates_normalized_statuses_and_expiry()
    {
        $now = now();
        $rows = [
            ['TR-001', 'Approved', $now->copy()->addYear()->format('Y-m-d')],
            ['TR-002', 'approved', $now->copy()->subDay()->format('Y-m-d')],
            ['TR-003', 'Pending', null],
            ['TR-004', 'Reviewed', null],
        ];

        foreach ($rows as $row) {
            DB::table('training_certificates')->insert([
                'certificate_number' => $row[0],
                'participant_name' => 'Test Participant',
                'status' => $row[1],
                'issue_date' => $now->format('Y-m-d'),
                'expiry_date' => $row[2],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
        DB::table('training_trainers')->insert([
            ['is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['is_active' => false, 'created_at' => $now, 'updated_at' => $now],
        ]);

        $data = app(DashboardService::class)->data();

        $this->assertSame(4, $data['stats']['total']);
        $this->assertSame(1, $data['stats']['approved']);
        $this->assertSame(1, $data['stats']['pending_review']);
        $this->assertSame(1, $data['stats']['pending_approval']);
        $this->assertSame(1, $data['stats']['expired']);
        $this->assertSame(1, $data['stats']['active_trainers']);
        $this->assertSame(4, array_sum($data['monthlyChart']['values']));
        $this->assertSame(0, $data['myAssignments']['total']);
    }

    public function test_my_assignments_count_only_current_user_work()
    {
        $now = now();
        DB::table('training_certificates')->insert([
            [
                'certificate_number' => 'TR-R1',
                'participant_name' => 'Review Me',
                'status' => 'Pending Review',
                'issue_date' => $now->format('Y-m-d'),
                'expiry_date' => null,
                'review_by_id' => 7,
                'approval_by_id' => 9,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'certificate_number' => 'TR-A1',
                'participant_name' => 'Approve Me',
                'status' => 'Pending Approval',
                'issue_date' => $now->format('Y-m-d'),
                'expiry_date' => null,
                'review_by_id' => 3,
                'approval_by_id' => 7,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'certificate_number' => 'TR-OTHER',
                'participant_name' => 'Someone Else',
                'status' => 'Pending Review',
                'issue_date' => $now->format('Y-m-d'),
                'expiry_date' => null,
                'review_by_id' => 99,
                'approval_by_id' => 98,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        $assignments = app(DashboardService::class)->myAssignments(7);

        $this->assertSame(1, $assignments['review']);
        $this->assertSame(1, $assignments['approval']);
        $this->assertSame(2, $assignments['total']);
    }

    public function test_authenticated_user_can_render_dashboard()
    {
        $user = new User([
            'name' => 'Dashboard User',
            'email' => 'dashboard@example.com',
            'designation' => 'Administrator',
        ]);
        $user->id = 1;

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Certificates Issued Over Time')
            ->assertSee('Recent Activities');
    }
}
