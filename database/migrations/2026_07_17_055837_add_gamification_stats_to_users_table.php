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
            $table->integer('points')->default(0);
            $table->decimal('total_co2_saved', 10, 2)->default(0.00);
            $table->integer('current_streak')->default(0);
            $table->date('last_activity_date')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['points', 'total_co2_saved', 'current_streak', 'last_activity_date']);
        });
    }
};
