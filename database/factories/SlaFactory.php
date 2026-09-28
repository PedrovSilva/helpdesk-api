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
    protected $model = Sla::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'priority' => fake()->unique()->randomElement(
                Priority::cases()
            ),
            'response_time_minutes' => fake()->numberBetween(
                15,
                480
            ),
            'resolution_time_minutes' => fake()->numberBetween(
                120,
                2880
            ),
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'active' => false,
        ]);
    }

    public function low(): static
    {
        return $this->state(fn () => [
            'name' => 'Low Priority SLA',
            'priority' => Priority::LOW,
            'response_time_minutes' => 480,
            'resolution_time_minutes' => 2880,
        ]);
    }

    public function medium(): static
    {
        return $this->state(fn () => [
            'name' => 'Medium Priority SLA',
            'priority' => Priority::MEDIUM,
            'response_time_minutes' => 240,
            'resolution_time_minutes' => 1440,
        ]);
    }

    public function high(): static
    {
        return $this->state(fn () => [
            'name' => 'High Priority SLA',
            'priority' => Priority::HIGH,
            'response_time_minutes' => 60,
            'resolution_time_minutes' => 480,
        ]);
    }

    public function critical(): static
    {
        return $this->state(fn () => [
            'name' => 'Critical Priority SLA',
            'priority' => Priority::CRITICAL,
            'response_time_minutes' => 15,
            'resolution_time_minutes' => 120,
        ]);
    }
}
