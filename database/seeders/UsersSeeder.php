<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User::factory()->create([
        //     'name' => 'John Doe',
        //     'phone_number' => 98939322820,
        //     'email' => 'john.doe@example.com',
        //     'username' => 'john',
        //     'password' => bcrypt('password'),
        // ]);
        User::factory()->count(50)->create();
    }
}
