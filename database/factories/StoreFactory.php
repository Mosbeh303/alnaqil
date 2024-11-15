<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company(),
            'website' => 'example.com',
            'city' => 'الرياض',
            'neighborhood' => $this->faker->city,
            'status' => 'approved',
            'base_fee' => $this->faker->numberBetween(10, 50),
            'exchange_fee' => $this->faker->numberBetween(10, 50),
            'refrigerated_transport_fee' => $this->faker->numberBetween(10, 50),
        ];
    }
}
