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
            $table->decimal('co2_saved', 8, 2)->default(0)->after('co2_equivalent');
            $table->integer('points_earned')->default(0)->after('co2_saved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carbon_logs', function (Blueprint $table) {
            $table->dropColumn(['co2_saved', 'points_earned']);
        });
    }
};
