<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'channel_id' => $this->faker->randomNumber(),
            'global_channel_id' => $this->faker->randomNumber(),
            'channel_order_no' => $this->faker->unique()->numerify('ORD-#####'),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'is_business_order' => $this->faker->boolean(),
            'billing_address' => $this->faker->address(),
            'shipping_address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'currency_code' => $this->faker->currencyCode(),
            'order_date' => $this->faker->dateTime()
        ];
    }
}
