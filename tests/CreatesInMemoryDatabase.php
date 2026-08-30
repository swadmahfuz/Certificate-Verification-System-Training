<?php

namespace Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait CreatesInMemoryDatabase
{
    protected function useInMemorySqlite(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        DB::purge('sqlite');
        DB::reconnect('sqlite');
    }

    protected function createActivityLogTables(array $appKeys = null): void
    {
        $appKeys = $appKeys ?? array_keys(config('cvs.apps', ['training' => 'Training CVS']));

        foreach ($appKeys as $appKey) {
            $table = $appKey . '_activity_logs';

            if (Schema::hasTable($table)) {
                continue;
            }

            Schema::create($table, function (Blueprint $table) {
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
}
