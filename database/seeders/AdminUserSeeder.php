<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Ensure a known admin account exists for the admin panel.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'role_id'    => 1,
                'first_name' => 'Super',
                'last_name'  => 'Admin',
                'password'   => Hash::make('Admin@12345'),
                'status'     => 'active',
            ]
        );
    }
}
