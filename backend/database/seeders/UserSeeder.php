<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
   public function run(): void 
   {
        $users = [
            [
                'name' => 'Dimas Raditya',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'no_hp' => '089509513171',
                'alamat' => 'Bandung, West Java',
            ],
            [
                'name' => 'Ilham Mamduh Al Ghifari',
                'email' => 'petugas@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'petugas',
                'no_hp' => '0895600015600',
                'alamat' => 'Rancamanyar, Bandung',
            ],
            [
                'name' => 'Christabell Artha Angelicareen',
                'email' => 'chrstbellartha@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'no_hp' => '0895678543231',
                'alamat' => 'Dago, Bandung',
            ],
            [
                'name' => 'Raka Hansel Alexsyah',
                'email' => 'rakahansel@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'no_hp' => '0895600015600',
                'alamat' => 'Ciparay, Bandung',
            ],
            [
                'name' => 'Athaya Asshauqi',
                'email' => 'athaya@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'peminjam',
                'no_hp' => '0895132435678',
                'alamat' => 'Ciparay, Bandung',      
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
   }
}
