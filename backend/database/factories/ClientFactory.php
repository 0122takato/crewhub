<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'contact_person' => fake()->name(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'postal_code' => fake()->postcode(),
            'address' => fake()->address(),
            'status' => 'active',
            'notes' => fake()->optional()->sentence(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}
