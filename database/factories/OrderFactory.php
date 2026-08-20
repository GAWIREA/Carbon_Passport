<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $payment = fake()->randomElement(['coins', 'points', 'idr']);

        return [
            'user_id' => 1,
            'product_id' => 1,
            'seller_id' => 2,
            'status' => fake()->randomElement(['pending', 'processing', 'shipped', 'completed']),
            'payment_method' => $payment,
            'total_points' => $payment === 'points' ? fake()->numberBetween(100, 2000) : 0,
            'total_coins' => $payment === 'coins' ? fake()->numberBetween(50, 1000) : 0,
            'total_idr' => $payment === 'idr' ? fake()->numberBetween(10000, 150000) : 0,
            'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
            'updated_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
