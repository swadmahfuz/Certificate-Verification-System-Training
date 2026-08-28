<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReportsActivityLogsTable extends Migration
{
    public function up()
    {
        Schema::create('reports_activity_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('event', 80);
            $table->string('subject_type', 50);
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            $table->string('causer_name')->nullable();
            $table->string('description', 500);
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['subject_type', 'subject_id'], 'reports_activity_subject_index');
            $table->index('event', 'reports_activity_event_index');
            $table->index('causer_id', 'reports_activity_causer_index');
            $table->index('created_at', 'reports_activity_created_index');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reports_activity_logs');
    }
}
