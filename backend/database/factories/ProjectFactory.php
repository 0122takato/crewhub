<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+3 months');
        $endDate = fake()->dateTimeBetween($startDate, '+1 week');

        $eventTypes = [
            'コンサート',
            '展示会',
            'スポーツイベント',
            '企業イベント',
            'フェスティバル',
            'セミナー',
            '発表会',
        ];

        return [
            'client_id' => Client::factory(),
            'created_by' => User::factory()->manager(),
            'title' => fake()->randomElement($eventTypes) . 'スタッフ募集',
            'description' => fake()->paragraph(),
            'venue_name' => fake()->randomElement(['東京ドーム', '幕張メッセ', '横浜アリーナ', 'さいたまスーパーアリーナ', '東京ビッグサイト']),
            'venue_address' => fake()->address(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'hourly_wage' => fake()->numberBetween(10, 20) * 100,
            'transportation_fee' => fake()->numberBetween(5, 15) * 100,
            'requirements' => "持ち物：\n- 黒のパンツ\n- 白のシャツ\n- スニーカー",
            'status' => 'published',
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'closed',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'start_date' => fake()->dateTimeBetween('-1 month', '-1 week'),
            'end_date' => fake()->dateTimeBetween('-1 week', 'now'),
        ]);
    }
}
