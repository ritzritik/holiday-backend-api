<?php

namespace Database\Factories;

use App\Models\UserVoucher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserVoucherFactory extends Factory
{
    protected $model = UserVoucher::class;

    /**
     * Define the model's default state.
     */
    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'user_booking_id' => \App\Models\UserBooking::factory(), // Add this line
            'voucher_code' => Str::random(10),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'currency' => 'GBP',
            'expiry_date' => $this->faker->dateTimeBetween('+1 week', '+1 year')->format('Y-m-d'),
            'terms_and_conditions' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['active', 'redeemed', 'expired']),
        ];
    }
}
