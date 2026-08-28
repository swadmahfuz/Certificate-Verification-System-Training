<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropDepartmentFromUsersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'department')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('department');
        });
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'department')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('department')->nullable()->after('email');
        });
    }
}
