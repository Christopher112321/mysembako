<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'owner',
                'description' => 'Pemilik toko',
            ],
            [
                'id' => 2,
                'name' => 'buyer',
                'description' => 'Pembeli',
            ],
            [
                'id' => 3,
                'name' => 'admin',
                'description' => 'Pengelola web',
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['id' => $role['id']],
                $role
            );
        }
    }
}

