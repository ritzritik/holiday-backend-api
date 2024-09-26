<?php

namespace Database\Seeders;

use App\Models\Flight;
use App\Models\AuthUser;
use App\Models\Package;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserBooking;
use App\Models\UserVoucher;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        AuthUser::factory()->create([
            'name' => 'User',
            'email' => 'user@gmail.com',
            'password' => bcrypt('123456789'),
            'user_type' => 1,
            'created_by' => null
        ]);


        User::factory()->create([
            'name' => 'John Doe',
            'phone_number' => 98939322820,
            'email' => 'john.doe@example.com',
            'username' => 'john',
            'password' => bcrypt('password'),
        ]);


        Flight::factory()->count(50)->create();
        Package::factory()->count(50)->create();
        UserBooking::factory()->count(10)->create();
        UserVoucher::factory()->count(5)->create();

    }
}
