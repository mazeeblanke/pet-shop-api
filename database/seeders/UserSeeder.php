<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // admin
        User::factory()->create([
            'email' => config('app.admin_email'),
            'password' => bcrypt(config('app.admin_password')),
            'is_admin' => 1
        ]);

        User::factory()->count(50)->create();
    }
}
