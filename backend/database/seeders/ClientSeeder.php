<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        // Create specific clients
        Client::create([
            'name' => '株式会社イベントプロ',
            'contact_person' => '山田太郎',
            'email' => 'contact@eventpro.example.com',
            'phone' => '03-1234-5678',
            'postal_code' => '100-0001',
            'address' => '東京都千代田区丸の内1-1-1',
            'status' => 'active',
        ]);

        Client::create([
            'name' => 'コンサート企画株式会社',
            'contact_person' => '佐藤花子',
            'email' => 'info@concert-plan.example.com',
            'phone' => '03-9876-5432',
            'postal_code' => '150-0001',
            'address' => '東京都渋谷区神宮前1-2-3',
            'status' => 'active',
        ]);

        // Create random clients
        Client::factory()->count(8)->create();
    }
}
