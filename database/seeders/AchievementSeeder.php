<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Seed the achievements table with initial achievements.
     */
    public function run(): void
    {
        $achievements = [
            // Logging milestones
            [
                'name' => 'Langkah Pertama',
                'description' => 'Lakukan input karbon pertamamu',
                'icon' => '🚀',
                'category' => 'Umum',
                'requirement_type' => 'total_logs',
                'requirement_value' => 1,
                'xp_reward' => 10,
            ],
            [
                'name' => 'Pencatat Pemula',
                'description' => 'Catat 10 aktivitas karbon',
                'icon' => '📝',
                'category' => 'Umum',
                'requirement_type' => 'total_logs',
                'requirement_value' => 10,
                'xp_reward' => 25,
            ],
            [
                'name' => 'Pencatat Rajin',
                'description' => 'Catat 50 aktivitas karbon',
                'icon' => '📊',
                'category' => 'Umum',
                'requirement_type' => 'total_logs',
                'requirement_value' => 50,
                'xp_reward' => 50,
            ],
            [
                'name' => 'Pencatat Handal',
                'description' => 'Catat 100 aktivitas karbon',
                'icon' => '🏅',
                'category' => 'Umum',
                'requirement_type' => 'total_logs',
                'requirement_value' => 100,
                'xp_reward' => 100,
            ],
            [
                'name' => 'Data Warrior',
                'description' => 'Catat 500 aktivitas karbon',
                'icon' => '⚔️',
                'category' => 'Umum',
                'requirement_type' => 'total_logs',
                'requirement_value' => 500,
                'xp_reward' => 250,
            ],

            // Streak milestones
            [
                'name' => 'Konsisten 3 Hari',
                'description' => 'Login streak 3 hari berturut-turut',
                'icon' => '🔥',
                'category' => 'Streak',
                'requirement_type' => 'streak',
                'requirement_value' => 3,
                'xp_reward' => 15,
            ],
            [
                'name' => 'Konsisten 7 Hari',
                'description' => 'Login streak 7 hari berturut-turut',
                'icon' => '🔥',
                'category' => 'Streak',
                'requirement_type' => 'streak',
                'requirement_value' => 7,
                'xp_reward' => 30,
            ],
            [
                'name' => 'Konsisten 30 Hari',
                'description' => 'Login streak 30 hari berturut-turut',
                'icon' => '💪',
                'category' => 'Streak',
                'requirement_type' => 'streak',
                'requirement_value' => 30,
                'xp_reward' => 100,
            ],

            // Level milestones
            [
                'name' => 'Daun Muda',
                'description' => 'Capai tingkat Daun',
                'icon' => '🌿',
                'category' => 'Level',
                'requirement_type' => 'level',
                'requirement_value' => 6,
                'xp_reward' => 50,
            ],
            [
                'name' => 'Pohon Kokoh',
                'description' => 'Capai tingkat Pohon',
                'icon' => '🌳',
                'category' => 'Level',
                'requirement_type' => 'level',
                'requirement_value' => 11,
                'xp_reward' => 100,
            ],
            [
                'name' => 'Penjaga Bumi',
                'description' => 'Capai tingkat Bumi',
                'icon' => '🌍',
                'category' => 'Level',
                'requirement_type' => 'level',
                'requirement_value' => 16,
                'xp_reward' => 200,
            ],
            [
                'name' => 'Cahaya Matahari',
                'description' => 'Capai tingkat Matahari',
                'icon' => '☀️',
                'category' => 'Level',
                'requirement_type' => 'level',
                'requirement_value' => 21,
                'xp_reward' => 500,
            ],

            // Mission milestones
            [
                'name' => 'Misi Pertama',
                'description' => 'Selesaikan misi pertamamu',
                'icon' => '🎯',
                'category' => 'Misi',
                'requirement_type' => 'mission_completed',
                'requirement_value' => 1,
                'xp_reward' => 15,
            ],
            [
                'name' => 'Misi Master',
                'description' => 'Selesaikan 10 misi',
                'icon' => '🎖️',
                'category' => 'Misi',
                'requirement_type' => 'mission_completed',
                'requirement_value' => 10,
                'xp_reward' => 50,
            ],
            [
                'name' => 'Misi Legend',
                'description' => 'Selesaikan 50 misi',
                'icon' => '👑',
                'category' => 'Misi',
                'requirement_type' => 'mission_completed',
                'requirement_value' => 50,
                'xp_reward' => 200,
            ],

            // CO2 saved milestones
            [
                'name' => 'Penyelamat Udara',
                'description' => 'Hemat total 10 kg CO₂',
                'icon' => '💨',
                'category' => 'Lingkungan',
                'requirement_type' => 'total_co2_saved',
                'requirement_value' => 10,
                'xp_reward' => 25,
            ],
            [
                'name' => 'Eco Warrior',
                'description' => 'Hemat total 100 kg CO₂',
                'icon' => '🛡️',
                'category' => 'Lingkungan',
                'requirement_type' => 'total_co2_saved',
                'requirement_value' => 100,
                'xp_reward' => 100,
            ],
            [
                'name' => 'Carbon Hero',
                'description' => 'Hemat total 500 kg CO₂',
                'icon' => '🦸',
                'category' => 'Lingkungan',
                'requirement_type' => 'total_co2_saved',
                'requirement_value' => 500,
                'xp_reward' => 300,
            ],

            // Social milestones
            [
                'name' => 'Sosial Butterfly',
                'description' => 'Dapatkan 5 followers',
                'icon' => '🦋',
                'category' => 'Sosial',
                'requirement_type' => 'followers',
                'requirement_value' => 5,
                'xp_reward' => 25,
            ],
            [
                'name' => 'Influencer Hijau',
                'description' => 'Dapatkan 20 followers',
                'icon' => '🌟',
                'category' => 'Sosial',
                'requirement_type' => 'followers',
                'requirement_value' => 20,
                'xp_reward' => 75,
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::firstOrCreate(
                ['name' => $achievement['name']],
                $achievement
            );
        }
    }
}
