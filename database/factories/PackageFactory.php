<?php

namespace Database\Factories;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Flight>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agent_id' => '144', // Using the default value
            'package_code' => $this->faker->bothify('PKG-###'),
            'package_name' => $this->faker->numberBetween(1, 50),
            'depart' => $this->faker->randomElement(['LGW', 'STN', 'LHR', 'LCY', 'SEN', 'LTN']),
            'countryid' => $this->faker->numberBetween(1, 200),
            'regionid' => $this->faker->numberBetween(1, 100),
            'areaid' => $this->faker->numberBetween(1, 100),
            'resortid' => $this->faker->numberBetween(1, 100),
            'depdate' => $this->faker->date(),
            'adults' => $this->faker->numberBetween(1, 10),
            'children' => $this->faker->numberBetween(0, 5),
            'duration' => $this->faker->numberBetween(1, 30), // Duration in days
            'price' => $this->faker->randomFloat(2, 50, 5000), // Price range
            'is_deleted' => 0, // Default value
            'created_by' => 1, // Assuming created by admin user with id 1
        ];
    }
}
