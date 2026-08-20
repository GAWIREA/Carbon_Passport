<?php

namespace Database\Factories;

use App\Models\Mission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Mission>
 */
class MissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'category' => fake()->randomElement(['Transportasi', 'Makanan', 'Energi & Listrik']),
            'activity_type' => fake()->word(),
            'target_amount' => fake()->numberBetween(3, 10),
            'reward_points' => fake()->numberBetween(20, 100),
            'reward_coins' => fake()->numberBetween(10, 50),
            'duration_days' => 7,
            'type' => fake()->randomElement(['daily', 'weekly']),
            'is_active' => true,
        ];
    }
}
