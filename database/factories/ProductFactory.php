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
            'is_active' => $this->faker->boolean(),
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'brand' => $this->faker->company(),
            'merchant_product_no' => $this->faker->unique()->numerify('MPN-#####'),
            'manufacturer_product_number' => $this->faker->unique()->numerify('MPN-#####'),
            'ean' => $this->faker->unique()->ean13(),
        ];
    }
}
