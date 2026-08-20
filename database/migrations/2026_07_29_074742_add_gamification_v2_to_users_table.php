<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('xp')->default(0)->after('last_activity_date');
            $table->unsignedInteger('coins')->default(0)->after('xp');
            $table->unsignedInteger('monthly_points')->default(0)->after('coins');
            $table->unsignedSmallInteger('level')->default(1)->after('monthly_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['xp', 'coins', 'monthly_points', 'level']);
        });
    }
};
