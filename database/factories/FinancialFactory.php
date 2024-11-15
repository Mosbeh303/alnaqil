<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FinancialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'cod' => $this->faker->numberBetween(10, 500),
            'base_fee' => $this->faker->numberBetween(10, 50),
            'refrigerated_transport_fee' => $this->faker->numberBetween(10, 50),
            'exchange_fee' => $this->faker->numberBetween(10, 50),
            'tax' => $this->faker->numberBetween(10, 50),
        ];
    }
}
