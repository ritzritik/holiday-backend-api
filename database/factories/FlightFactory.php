<?php

namespace Database\Factories;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flight>
 */
class FlightFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'flight_number' => $this->faker->bothify('FL-###'),
            'flight_name' => $this->faker->word . ' ' . $this->faker->word,
            'departure' => $this->faker->city,
            'departure_date' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'arrival' => $this->faker->city,
            'departure_time' => $this->faker->dateTimeBetween('+1 day', '+1 week'),
            'arrival_time' => $this->faker->dateTimeBetween('+1 day', '+1 week'),
            'duration' => $this->faker->time,
            'price' => $this->faker->randomFloat(2, 50, 500),
            'stops' => $this->faker->randomElement(['non_stop', '1_stop', '2_stops']),
            'is_deleted' => 0,
            'created_by' => 1,
        ];
    }
}
