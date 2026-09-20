<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Owner account (pemilik toko)
        User::updateOrCreate(
            ['email' => 'owner@mysembako.com'],
            [
                'name' => 'Owner Toko',
                'email' => 'owner@mysembako.com',
                'password' => Hash::make('owner123'),
                'role_id' => 1, // Owner role (pemilik toko)
                'phone' => null,
            ]
        );

        // Create Web Admin account (pengelola web)
        User::updateOrCreate(
            ['email' => 'admin@mysembako.com'],
            [
                'name' => 'Web Administrator',
                'email' => 'admin@mysembako.com',
                'password' => Hash::make('admin123'),
                'role_id' => 3, // Admin role (pengelola web)
                'phone' => null,
            ]
        );

        $this->command->info('Owner user created successfully!');
        $this->command->info('Email: owner@mysembako.com');
        $this->command->info('Password: owner123');
        $this->command->info('');
        $this->command->info('Web Admin user created successfully!');
        $this->command->info('Email: admin@mysembako.com');
        $this->command->info('Password: admin123');
    }
}

