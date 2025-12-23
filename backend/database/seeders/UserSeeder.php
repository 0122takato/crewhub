<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\StaffProfile;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => '管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Manager user
        $manager = User::create([
            'name' => 'マネージャー田中',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Client user
        $client = User::create([
            'name' => 'クライアント鈴木',
            'email' => 'client@example.com',
            'password' => Hash::make('password'),
            'role' => 'client',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Staff users with profiles
        $staffUsers = User::factory()
            ->count(20)
            ->staff()
            ->create();

        $skills = Skill::all();

        foreach ($staffUsers as $user) {
            StaffProfile::factory()
                ->for($user)
                ->verified()
                ->create();

            // Attach random skills
            $user->skills()->attach(
                $skills->random(rand(2, 5))->pluck('id'),
                ['verified' => fake()->boolean(70)]
            );
        }

        // Pending staff users
        $pendingStaff = User::factory()
            ->count(5)
            ->staff()
            ->pending()
            ->create();

        foreach ($pendingStaff as $user) {
            StaffProfile::factory()
                ->for($user)
                ->create();
        }
    }
}
