<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'allanjacob03@gmail.com',
            ],
            [
                'name' => 'Allan Jacob',
                'email_verified_at' => now(),
                'password' => Hash::make('admin@1234567890'),
                'remember_token' => null,
                'organization_id' => 1,
                'employee_id' => 1,
                'status' => 'active',
                'last_login_at' => null,
                'two_factor_enabled' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
