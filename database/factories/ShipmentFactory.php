<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'number' => str_pad(mt_rand(1,999999999),10,'1',STR_PAD_LEFT),
            'status' => 10,
            'receiver_name' => $this->faker->name(),
            'city' => $this->faker->city,
            'neighborhood' => $this->faker->city,
            'receiver_phone' => $this->faker->tollFreePhoneNumber,
            'details' => $this->faker->word(),
            'exchange' => $this->faker->numberBetween(0, 1),
            'refrigerated'  => $this->faker->numberBetween(0, 1),
        ];
    }
}
