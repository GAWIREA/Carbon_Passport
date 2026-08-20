<?php

namespace Database\Seeders;

use App\Models\Mission;
use Illuminate\Database\Seeder;

class MissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mission::firstOrCreate([
            'activity_type' => 'sepeda',
        ], [
            'title' => 'Naik sepeda ke kantor 3x',
            'category' => 'Transportasi',
            'target_amount' => 3,
            'reward_points' => 40,
            'reward_coins' => 30,
            'duration_days' => 7,
            'type' => 'weekly',
            'is_active' => true,
        ]);

        Mission::firstOrCreate([
            'activity_type' => 'botol',
        ], [
            'title' => 'Bawa botol minum sendiri 5x',
            'category' => 'Lifestyle',
            'target_amount' => 5,
            'reward_points' => 30,
            'reward_coins' => 20,
            'duration_days' => 7,
            'type' => 'weekly',
            'is_active' => true,
        ]);

        Mission::firstOrCreate([
            'activity_type' => 'krl',
        ], [
            'title' => 'Pergi dengan KRL 4x',
            'category' => 'Transportasi',
            'target_amount' => 4,
            'reward_points' => 50,
            'reward_coins' => 40,
            'duration_days' => 7,
            'type' => 'weekly',
            'is_active' => true,
        ]);
    }
}
