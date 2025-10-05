<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::create([
            'name' => 'أحمد محمد علي',
            'email' => 'ahmed@example.com',
            'phone' => '0501234567',
            'address' => 'الرياض، حي الملز',
            'national_id' => '1234567890',
            'is_active' => true,
        ]);

        Client::create([
            'name' => 'سارة أحمد السعيد',
            'email' => 'sara@example.com',
            'phone' => '0509876543',
            'address' => 'جدة، حي الروضة',
            'national_id' => '0987654321',
            'is_active' => true,
        ]);

        Client::create([
            'name' => 'محمد عبد الله النصر',
            'email' => 'mohammed@example.com',
            'phone' => '0551122334',
            'address' => 'الدمام، حي الشاطئ',
            'national_id' => '1122334455',
            'is_active' => true,
        ]);
    }
}