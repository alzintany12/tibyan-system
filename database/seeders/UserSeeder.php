<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'مسؤول النظام',
            'email' => 'admin@tibyan.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'محامي تجريبي',
            'email' => 'lawyer@tibyan.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}