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
        Schema::table('carbon_logs', function (Blueprint $table) {
            $table->unsignedInteger('xp_earned')->default(0)->after('points_earned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carbon_logs', function (Blueprint $table) {
            $table->dropColumn('xp_earned');
        });
    }
};
