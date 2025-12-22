<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StaffProfile>
 */
class StaffProfileFactory extends Factory
{
    public function definition(): array
    {
        $prefectures = ['東京都', '神奈川県', '大阪府', '愛知県', '福岡県', '北海道', '埼玉県', '千葉県'];

        return [
            'user_id' => User::factory(),
            'phone' => fake()->phoneNumber(),
            'date_of_birth' => fake()->dateTimeBetween('-50 years', '-18 years'),
            'gender' => fake()->randomElement(['male', 'female', 'other']),
            'postal_code' => fake()->postcode(),
            'prefecture' => fake()->randomElement($prefectures),
            'city' => fake()->city(),
            'address' => fake()->streetAddress(),
            'bank_name' => fake()->randomElement(['三菱UFJ銀行', 'みずほ銀行', '三井住友銀行', 'りそな銀行']),
            'bank_branch' => fake()->city() . '支店',
            'bank_account_type' => 'ordinary',
            'bank_account_number' => fake()->numerify('#######'),
            'bank_account_holder' => fake()->name(),
            'bio' => fake()->optional()->sentence(),
        ];
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'id_verified_at' => now(),
        ]);
    }
}
