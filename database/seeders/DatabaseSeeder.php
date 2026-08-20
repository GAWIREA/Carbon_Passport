<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed achievements first (master data)
        $this->call(AchievementSeeder::class);

        // Demo user — role: Karyawan
        $user = User::factory()->create([
            'name' => 'Nadia Putri',
            'email' => 'user@ecotrack.id',
            'xp' => 150,
            'level' => 3,
            'coins' => 50,
            'monthly_points' => 120,
        ]);

        // Demo seller
        $seller = User::factory()->seller()->create([
            'name' => 'Toko Hijau',
            'email' => 'seller@ecotrack.id',
        ]);

        // Demo admin
        $admin = User::factory()->admin()->create([
            'name' => 'HR Admin',
            'email' => 'admin@ecotrack.id',
        ]);

        // Seed 50 Carbon Logs for User
        \App\Models\CarbonLog::factory(50)->create(['user_id' => $user->id]);

        // Seed Missions (static + factory)
        $this->call(MissionSeeder::class);
        $missions = \App\Models\Mission::factory(20)->create();

        // Seed User Missions (attach 50 user missions randomly)
        foreach (range(1, 50) as $i) {
            \App\Models\UserMission::factory()->create([
                'user_id' => $user->id,
                'mission_id' => $missions->random()->id,
            ]);
        }

        // Seed Products for Seller (with coin_price)
        $products = \App\Models\Product::factory(50)->create(['seller_id' => $seller->id]);

        // Seed Orders for Seller and User
        foreach (range(1, 50) as $i) {
            \App\Models\Order::factory()->create([
                'user_id' => $user->id,
                'seller_id' => $seller->id,
                'product_id' => $products->random()->id,
            ]);
        }
    }
}
