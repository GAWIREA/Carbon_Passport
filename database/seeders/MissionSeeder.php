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
        // Hapus misi lama agar tidak menumpuk
        Mission::query()->delete();

        // Weekly Missions (3 misi)
        Mission::create([
            'activity_type' => 'motor',
            'title' => 'Naik motor ke kantor 3x',
            'category' => 'transportasi',
            'target_amount' => 3,
            'reward_points' => 40,
            'reward_coins' => 30,
            'duration_days' => 7,
            'type' => 'weekly',
            'is_active' => true,
        ]);

        Mission::create([
            'activity_type' => 'bus',
            'title' => 'Pergi dengan bus 5x',
            'category' => 'transportasi',
            'target_amount' => 5,
            'reward_points' => 50,
            'reward_coins' => 40,
            'duration_days' => 7,
            'type' => 'weekly',
            'is_active' => true,
        ]);

        Mission::create([
            'activity_type' => 'sampah_organik',
            'title' => 'Kelola sampah organik 4x',
            'category' => 'limbah',
            'target_amount' => 4,
            'reward_points' => 30,
            'reward_coins' => 20,
            'duration_days' => 7,
            'type' => 'weekly',
            'is_active' => true,
        ]);

        // Daily Missions (3 misi)
        Mission::create([
            'activity_type' => 'listrik_plts',
            'title' => 'Gunakan listrik PLTS hari ini',
            'category' => 'energi',
            'target_amount' => 1,
            'reward_points' => 15,
            'reward_coins' => 10,
            'duration_days' => 1,
            'type' => 'daily',
            'is_active' => true,
        ]);

        Mission::create([
            'activity_type' => 'makanan_nabati',
            'title' => 'Konsumsi makanan nabati',
            'category' => 'makanan',
            'target_amount' => 1,
            'reward_points' => 20,
            'reward_coins' => 15,
            'duration_days' => 1,
            'type' => 'daily',
            'is_active' => true,
        ]);

        Mission::create([
            'activity_type' => 'sepeda',
            'title' => 'Catat 1 aktivitas bersepeda',
            'category' => 'transportasi',
            'target_amount' => 1,
            'reward_points' => 15,
            'reward_coins' => 10,
            'duration_days' => 1,
            'type' => 'daily',
            'is_active' => true,
        ]);
    }
}
