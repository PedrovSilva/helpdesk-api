<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TicketHistory>
 */
class TicketHistoryFactory extends Factory
{
    protected $model = TicketHistory::class;

    public function definition(): array
    {
        $event = fake()->randomElement([
            'created',
            'status_changed',
            'priority_changed',
            'assigned',
        ]);

        return [
            'ticket_id' => Ticket::factory(),
            'user_id' => User::factory(),
            'event' => $event,
            'from_value' => fake()->optional()->word(),
            'to_value' => fake()->optional()->word(),
            'metadata' => fake()->boolean()
                ? ['source' => 'api']
                : null,
        ];
    }
}
