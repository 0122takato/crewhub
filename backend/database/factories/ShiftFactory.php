<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Shift>
 */
class ShiftFactory extends Factory
{
    public function definition(): array
    {
        $startHour = fake()->numberBetween(8, 18);

        return [
            'project_id' => Project::factory(),
            'date' => fake()->dateTimeBetween('now', '+1 month'),
            'start_time' => sprintf('%02d:00', $startHour),
            'end_time' => sprintf('%02d:00', $startHour + fake()->numberBetween(4, 8)),
            'break_minutes' => fake()->randomElement([0, 30, 60]),
            'capacity' => fake()->numberBetween(3, 20),
            'confirmed_count' => 0,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
