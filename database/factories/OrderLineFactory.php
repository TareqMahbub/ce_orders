<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderLineFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => 0, // pass as parameter
            'product_id' => 0, // pass as parameter
            'merchant_product_no' => $this->faker->unique()->numerify('MPN-#####'),
            'gtin' => '', // pass as parameter

            'quantity' => $this->faker->numberBetween(1, 100),
            'description' => $this->faker->sentence(),
            'ce_order_line_id' => $this->faker->numberBetween(111111, 999999),
            'channel_order_line_no' => $this->faker->unique()->numerify('COLN-#####'),
            'is_fulfillment_by_marketplace' => $this->faker->boolean(),
            'channel_product_no' => $this->faker->numberBetween(111111, 999999),
            'stock_location_id' => $this->faker->numberBetween(111111, 999999),
        ];
    }
}
