<?php

namespace Database\Factories;

use App\Models\UserBooking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserBookingFactory extends Factory
{
    protected $model = UserBooking::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'booking_reference' => Str::random(10),
            'booking_type' => $this->faker->randomElement(['flight', 'hotel', 'package', 'holiday']),
            'booking_details' => $this->faker->sentence(), // Updated field name
            'price' => $this->faker->randomFloat(2, 100, 2000),
            'currency' => 'GBP', // Default currency is Pound
            'check_in_date' => $this->faker->date(),
            'check_out_date' => $this->faker->date(),
        ];
    }
}
