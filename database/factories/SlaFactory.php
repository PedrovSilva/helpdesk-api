<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Models\Sla;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sla>
 */
class SlaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'priority' => fake()->randomElement([Priority::LOW, Priority::MEDIUM, Priority::HIGH]),
            'response_time' => fake()->numberBetween(1, 24),
            'resolution_time' => fake()->numberBetween(1, 72),
            'is_active' => fake()->boolean(),
        ];
    }
}
