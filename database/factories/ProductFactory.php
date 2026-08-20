<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'seller_id' => 2, // Default seller id
            'name' => fake()->randomElement(['Tumbler Eco', 'Sedotan Bambu', 'Tas Kain', 'Voucher KRL', 'Voucher MRT', 'Buku Daur Ulang']),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['Barang Ramah Lingkungan', 'Voucher Transportasi', 'Layanan']),
            'point_price' => fake()->numberBetween(100, 2000),
            'coin_price' => fake()->numberBetween(50, 1000),
            'idr_price' => fake()->numberBetween(10000, 150000),
            'stock' => fake()->numberBetween(0, 100),
            'type' => fake()->randomElement(['voucher', 'physical']),
        ];
    }
}
