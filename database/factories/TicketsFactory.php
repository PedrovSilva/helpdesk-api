<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Models\Categories;
use App\Models\Tickets;
use App\Models\User;
use App\Models\Slas;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tickets>
 */
class TicketsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_number' => fake()->uuid(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'slas_id' => Slas::factory(),
            'status' => fake()->randomElement(TicketStatus::cases()),
            'priority' => fake()->randomElement(Priority::cases()),
            'category_id' => Categories::factory(),
            'customer_id' => User::factory(),
            'assigned_to' => User::factory(),
            'sla_due_date' => fake()->dateTimeBetween('+1 days', '+7 days'),
            'resolved_at' => fake()->optional()->dateTimeBetween('+1 days', '+7 days'),
            'closed_at' => fake()->optional()->dateTimeBetween('+1 days', '+7 days'),
        ];
    }
}
