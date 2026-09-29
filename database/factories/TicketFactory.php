<?php

namespace Database\Factories;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Sla;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        $priority = Priority::MEDIUM;

        return [
            'ticket_number' => (string) Str::uuid(),
            'title' => fake()->sentence(6),
            'description' => fake()->paragraph(),
            'status' => TicketStatus::OPEN,
            'priority' => $priority,
            'category_id' => Category::factory(),
            'sla_id' => $this->slaIdFor($priority),
            'customer_id' => User::factory()->customer(),
            'assigned_to' => null,
            'sla_due_date' => now()->addDay(),
            'resolved_at' => null,
            'closed_at' => null,
        ];
    }

    public function inProgress(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::IN_PROGRESS,
        ]);
    }

    public function resolved(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::RESOLVED,
            'resolved_at' => now(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn () => [
            'status' => TicketStatus::CLOSED,
            'resolved_at' => now(),
            'closed_at' => now(),
        ]);
    }

    public function high(): static
    {
        return $this->state(function () {
            $priority = Priority::HIGH;

            return [
                'priority' => $priority,
                'sla_id' => $this->slaIdFor($priority),
            ];
        });
    }

    protected function slaIdFor(Priority $priority): int
    {
        $existing = Sla::query()
            ->where('priority', $priority)
            ->value('id');

        if ($existing !== null) {
            return $existing;
        }

        $factory = Sla::factory();

        return match ($priority) {
            Priority::LOW => $factory->low()->create()->id,
            Priority::MEDIUM => $factory->medium()->create()->id,
            Priority::HIGH => $factory->high()->create()->id,
            Priority::CRITICAL => $factory->critical()->create()->id,
        };
    }
}
