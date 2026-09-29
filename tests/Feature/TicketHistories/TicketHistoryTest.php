<?php

namespace Tests\Feature\TicketHistories;

use App\Models\Ticket;
use App\Models\TicketHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_ticket_histories(): void
    {
        TicketHistory::factory()->count(3)->create();

        $this->getJson('/api/v1/ticket-histories')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_view_ticket_history(): void
    {
        $ticketHistory = TicketHistory::factory()->create();

        $this->getJson("/api/v1/ticket-histories/{$ticketHistory->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $ticketHistory->id)
            ->assertJsonPath('data.ticket_id', $ticketHistory->ticket_id)
            ->assertJsonPath('data.user_id', $ticketHistory->user_id)
            ->assertJsonPath('data.event', $ticketHistory->event)
            ->assertJsonPath('data.from_value', $ticketHistory->from_value)
            ->assertJsonPath('data.to_value', $ticketHistory->to_value);
    }

    public function test_returns_404_for_nonexistent_ticket_history(): void
    {
        $this->getJson('/api/v1/ticket-histories/999999')
            ->assertNotFound();
    }

    public function test_can_create_ticket_history(): void
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->agent()->create();

        $payload = [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'event' => 'status_changed',
            'from_value' => 'open',
            'to_value' => 'in_progress',
            'metadata' => [
                'source' => 'api',
            ],
        ];

        $this->postJson('/api/v1/ticket-histories', $payload)
            ->assertCreated()
            ->assertJsonPath('data.ticket_id', $ticket->id)
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.event', 'status_changed')
            ->assertJsonPath('data.from_value', 'open')
            ->assertJsonPath('data.to_value', 'in_progress')
            ->assertJsonPath('data.metadata.source', 'api');

        $this->assertDatabaseHas('ticket_histories', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'event' => 'status_changed',
            'from_value' => 'open',
            'to_value' => 'in_progress',
        ]);
    }

    public function test_ticket_history_requires_ticket_user_and_event(): void
    {
        $this->postJson('/api/v1/ticket-histories', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'ticket_id',
                'user_id',
                'event',
            ]);
    }

    public function test_ticket_must_exist(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/v1/ticket-histories', [
            'ticket_id' => 999999,
            'user_id' => $user->id,
            'event' => 'created',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('ticket_id');
    }

    public function test_user_must_exist(): void
    {
        $ticket = Ticket::factory()->create();

        $this->postJson('/api/v1/ticket-histories', [
            'ticket_id' => $ticket->id,
            'user_id' => 999999,
            'event' => 'created',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('user_id');
    }

    public function test_metadata_must_be_an_array(): void
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();

        $this->postJson('/api/v1/ticket-histories', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'event' => 'created',
            'metadata' => 'invalid',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('metadata');
    }

    public function test_can_update_ticket_history(): void
    {
        $ticketHistory = TicketHistory::factory()->create();

        $this->putJson("/api/v1/ticket-histories/{$ticketHistory->id}", [
            'event' => 'assigned',
            'from_value' => null,
            'to_value' => 'agent-1',
            'metadata' => [
                'reason' => 'manual',
            ],
        ])
            ->assertOk()
            ->assertJsonPath('data.id', $ticketHistory->id)
            ->assertJsonPath('data.event', 'assigned')
            ->assertJsonPath('data.from_value', null)
            ->assertJsonPath('data.to_value', 'agent-1')
            ->assertJsonPath('data.metadata.reason', 'manual');

        $this->assertDatabaseHas('ticket_histories', [
            'id' => $ticketHistory->id,
            'event' => 'assigned',
            'to_value' => 'agent-1',
        ]);
    }

    public function test_returns_404_when_updating_nonexistent_ticket_history(): void
    {
        $this->putJson('/api/v1/ticket-histories/999999', [
            'event' => 'created',
        ])
            ->assertNotFound();
    }

    public function test_can_delete_ticket_history(): void
    {
        $ticketHistory = TicketHistory::factory()->create();

        $this->deleteJson("/api/v1/ticket-histories/{$ticketHistory->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('ticket_histories', [
            'id' => $ticketHistory->id,
        ]);
    }

    public function test_returns_404_when_deleting_nonexistent_ticket_history(): void
    {
        $this->deleteJson('/api/v1/ticket-histories/999999')
            ->assertNotFound();
    }
}
