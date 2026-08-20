<?php

namespace Database\Factories;

use App\Models\UserMission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserMission>
 */
class UserMissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['active', 'done', 'expired']);
        $started = fake()->dateTimeBetween('-1 month', 'now');
        $completed = $status === 'done' ? (clone $started)->modify('+'.fake()->numberBetween(1,6).' days') : null;

        return [
            'user_id' => 1,
            'mission_id' => 1,
            'current_progress' => fake()->numberBetween(0, 10),
            'status' => $status,
            'started_at' => $started,
            'completed_at' => $completed,
        ];
    }
}
