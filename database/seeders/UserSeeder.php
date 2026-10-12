<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Ahmad Fauzi',
                'username' => 'ahmadfauzi',
                'email' => 'ahmad@example.com',
                'password' => Hash::make('password123'),
                'nim' => '220194850',
                'fakultas' => 'Fakultas Ilmu Komputer'
            ],
            [
                'name' => 'Budi Santoso',
                'username' => 'budisantoso',
                'email' => 'budi@example.com',
                'password' => Hash::make('password123'),
                'nim' => '220194851',
                'fakultas' => 'Fakultas Ekonomi'
            ],
            [
                'name' => 'Siti Rahayu',
                'username' => 'sitirahayu',
                'email' => 'siti@example.com',
                'password' => Hash::make('password123'),
                'nim' => '220194852',
                'fakultas' => 'Fakultas Hukum'
            ],
            [
                'name' => 'Andi Wijaya',
                'username' => 'andiwijaya',
                'email' => 'andi@example.com',
                'password' => Hash::make('password123'),
                'nim' => '220194853',
                'fakultas' => 'Fakultas Teknik'
            ]
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}