<?php

namespace Database\Factories;

use App\Models\Ticket_histories;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket_histories>
 */
class TicketHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'event' => fake()->word(),
            'from_value' => fake()->word(),
            'to_value' => fake()->word(),
            'metadata' => json_encode(['key' => fake()->word(), 'value' => fake()->word()]),

        ];
    }
}
