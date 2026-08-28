<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddRbacToUsersTable extends Migration
{
    private const APP_KEYS = [
        'training',
        'inspection',
        'calibration',
        'reports',
        'certification',
    ];

    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_super_admin')) {
                $table->boolean('is_super_admin')->default(false)->after('designation');
            }

            if (!Schema::hasColumn('users', 'department_id')) {
                $table->unsignedBigInteger('department_id')->nullable()->after('designation');
                $table->foreign('department_id')
                    ->references('id')
                    ->on('departments')
                    ->nullOnDelete();
            }
        });

        if (!Schema::hasTable('user_app_permissions')) {
            Schema::create('user_app_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('app_key', 32);
                $table->string('access_level', 16);
                $table->timestamps();

                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->cascadeOnDelete();

                $table->unique(['user_id', 'app_key']);
                $table->index('app_key');
            });
        }

        if (!Schema::hasTable('departments')) {
            return;
        }

        $legacyDepartments = DB::table('users')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->distinct()
            ->pluck('department');

        foreach ($legacyDepartments as $name) {
            $trimmed = trim((string) $name);

            if ($trimmed === '') {
                continue;
            }

            DB::table('departments')->updateOrInsert(
                ['name' => $trimmed],
                ['is_active' => true, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        $departmentMap = DB::table('departments')->pluck('id', 'name');

        DB::table('users')
            ->whereNotNull('department')
            ->where('department', '!=', '')
            ->orderBy('id')
            ->chunkById(100, function ($users) use ($departmentMap) {
                foreach ($users as $user) {
                    $name = trim((string) $user->department);

                    if ($name === '' || !isset($departmentMap[$name])) {
                        continue;
                    }

                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['department_id' => $departmentMap[$name]]);
                }
            });

        $existingUserIds = DB::table('users')->pluck('id');

        foreach ($existingUserIds as $userId) {
            foreach (self::APP_KEYS as $appKey) {
                $exists = DB::table('user_app_permissions')
                    ->where('user_id', $userId)
                    ->where('app_key', $appKey)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('user_app_permissions')->insert([
                    'user_id' => $userId,
                    'app_key' => $appKey,
                    'access_level' => 'full',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('user_app_permissions');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'department_id')) {
                $table->dropForeign(['department_id']);
                $table->dropColumn('department_id');
            }

            if (Schema::hasColumn('users', 'is_super_admin')) {
                $table->dropColumn('is_super_admin');
            }
        });
    }
}
