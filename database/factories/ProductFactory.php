<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Random\RandomException;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function definition(): array
    {
        return [
            'is_active' => random_int(0,1) === 1,
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'brand' => fake()->company(),
            'merchant_product_no' => fake()->unique()->numerify('MPN-#####'),
            'manufacturer_product_number' => fake()->unique()->numerify('MPN-#####'),
            'ean' => fake()->unique()->ean13(),
        ];
    }
}
