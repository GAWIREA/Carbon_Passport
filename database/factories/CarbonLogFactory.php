<?php

namespace Database\Factories;

use App\Models\CarbonLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CarbonLog>
 */
class CarbonLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            'Transportasi' => ['Bensin Motor', 'Bensin Mobil', 'KRL / MRT', 'Ojek Online', 'Bus Umum'],
            'Makanan' => ['Daging Sapi', 'Ayam', 'Makanan Nabati', 'Fast Food'],
            'Energi & Listrik' => ['Listrik Rumah', 'AC', 'Elektronik'],
        ];

        $category = fake()->randomElement(array_keys($categories));
        $activity = fake()->randomElement($categories[$category]);

        return [
            'user_id' => 1,
            'category' => $category,
            'activity_type' => $activity,
            'amount' => fake()->randomFloat(2, 1, 50),
            'unit' => fake()->randomElement(['Liter', 'KM', 'Porsi', 'kWh']),
            'co2_equivalent' => fake()->randomFloat(2, 0.5, 20),
            'co2_saved' => fake()->randomFloat(2, 0, 5),
            'points_earned' => fake()->numberBetween(0, 50),
            'xp_earned' => fake()->randomElement([0, 20]),
            'date' => fake()->dateTimeBetween('-6 months', 'now')->format('Y-m-d'),
        ];
    }
}
