<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Bima Fahrudin',
            'email' => 'bima@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Agustio Fathoni',
            'email' => 'toni@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Aryoko',
            'email' => 'aryoko@gmail.com',
            'password' => Hash::make('password123'),
        ]);

        User::create([
            'name' => 'Arya',
            'email' => 'arya@gmail.com',
            'password' => Hash::make('password123'),
        ]);
        User::create([
            'name' => 'Naufal Afdillah',
            'email' => 'nopal@gmail.com',
            'password' => Hash::make('password123'),
        ]);
    }
}
