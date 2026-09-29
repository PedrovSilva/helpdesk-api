<?php

namespace Tests\Feature\Tickets;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Models\Category;
use App\Models\Sla;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_tickets(): void
    {
        Ticket::factory()->count(3)->create();

        $this->getJson('/api/v1/tickets')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_filter_tickets_by_status(): void
    {
        Ticket::factory()->create();
        $inProgress = Ticket::factory()->inProgress()->create();

        $this->getJson('/api/v1/tickets?status=in_progress')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $inProgress->id);
    }

    public function test_can_filter_tickets_by_priority(): void
    {
        Ticket::factory()->create();
        $high = Ticket::factory()->high()->create();

        $this->getJson('/api/v1/tickets?priority=high')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $high->id);
    }

    public function test_can_filter_tickets_by_category_id(): void
    {
        $category = Category::factory()->create();
        Ticket::factory()->create();
        $ticket = Ticket::factory()->create([
            'category_id' => $category->id,
        ]);

        $this->getJson("/api/v1/tickets?category_id={$category->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ticket->id);
    }

    public function test_can_filter_tickets_by_customer_id(): void
    {
        $customer = User::factory()->customer()->create();
        Ticket::factory()->create();
        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $this->getJson("/api/v1/tickets?customer_id={$customer->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ticket->id);
    }

    public function test_can_filter_tickets_by_assigned_to(): void
    {
        $agent = User::factory()->agent()->create();
        Ticket::factory()->create();
        $ticket = Ticket::factory()->inProgress()->create([
            'assigned_to' => $agent->id,
        ]);

        $this->getJson("/api/v1/tickets?assigned_to={$agent->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ticket->id);
    }

    public function test_can_filter_tickets_by_sla_id(): void
    {
        $mediumSla = Sla::factory()->medium()->create();
        $highSla = Sla::factory()->high()->create();

        Ticket::factory()->create([
            'sla_id' => $mediumSla->id,
        ]);
        $ticket = Ticket::factory()->high()->create([
            'sla_id' => $highSla->id,
        ]);

        $this->getJson("/api/v1/tickets?sla_id={$highSla->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ticket->id);
    }

    public function test_can_combine_ticket_filters(): void
    {
        $category = Category::factory()->create();
        Ticket::factory()->inProgress()->create();
        Ticket::factory()->create([
            'category_id' => $category->id,
        ]);
        $ticket = Ticket::factory()->inProgress()->create([
            'category_id' => $category->id,
        ]);

        $this->getJson("/api/v1/tickets?status=in_progress&category_id={$category->id}")
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $ticket->id);
    }

    public function test_ticket_filters_must_be_valid(): void
    {
        $this->getJson('/api/v1/tickets?status=urgent&priority=urgent&category_id=999999&customer_id=999999&assigned_to=999999&sla_id=999999')
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'status',
                'priority',
                'category_id',
                'customer_id',
                'assigned_to',
                'sla_id',
            ]);
    }

    public function test_assigned_to_filter_must_be_an_agent(): void
    {
        $customer = User::factory()->customer()->create();

        $this->getJson("/api/v1/tickets?assigned_to={$customer->id}")
            ->assertUnprocessable()
            ->assertJsonValidationErrors('assigned_to');
    }

    public function test_customer_id_filter_must_be_a_customer(): void
    {
        $agent = User::factory()->agent()->create();

        $this->getJson("/api/v1/tickets?customer_id={$agent->id}")
            ->assertUnprocessable()
            ->assertJsonValidationErrors('customer_id');
    }

    public function test_can_view_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $this->getJson("/api/v1/tickets/{$ticket->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $ticket->id)
            ->assertJsonPath('data.ticket_number', $ticket->ticket_number)
            ->assertJsonPath('data.title', $ticket->title)
            ->assertJsonPath('data.description', $ticket->description)
            ->assertJsonPath('data.status', TicketStatus::OPEN->value)
            ->assertJsonPath('data.priority', Priority::MEDIUM->value)
            ->assertJsonPath('data.category_id', $ticket->category_id)
            ->assertJsonPath('data.customer_id', $ticket->customer_id);
    }

    public function test_returns_404_for_nonexistent_ticket(): void
    {
        $this->getJson('/api/v1/tickets/999999')
            ->assertNotFound();
    }

    public function test_can_create_ticket(): void
    {
        $category = Category::factory()->create();
        $customer = User::factory()->customer()->create();
        $sla = Sla::factory()->medium()->create();

        $payload = [
            'title' => 'Cannot log in',
            'description' => 'The portal returns an unexpected error.',
            'priority' => 'medium',
            'category_id' => $category->id,
            'customer_id' => $customer->id,
        ];

        $this->postJson('/api/v1/tickets', $payload)
            ->assertCreated()
            ->assertJsonPath('data.title', 'Cannot log in')
            ->assertJsonPath(
                'data.description',
                'The portal returns an unexpected error.'
            )
            ->assertJsonPath('data.status', 'open')
            ->assertJsonPath('data.priority', 'medium')
            ->assertJsonPath('data.category_id', $category->id)
            ->assertJsonPath('data.customer_id', $customer->id)
            ->assertJsonPath('data.sla_id', $sla->id)
            ->assertJsonPath('data.assigned_to', null);

        $this->assertDatabaseHas('tickets', [
            'title' => 'Cannot log in',
            'category_id' => $category->id,
            'customer_id' => $customer->id,
            'sla_id' => $sla->id,
            'status' => 'open',
        ]);
    }

    public function test_ticket_generates_ticket_number_automatically(): void
    {
        $ticket = Ticket::factory()->create();

        $this->assertNotNull($ticket->ticket_number);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
        ]);
    }

    public function test_ticket_assigns_sla_due_date_from_resolution_time(): void
    {
        $this->freezeTime();

        $category = Category::factory()->create();
        $customer = User::factory()->customer()->create();
        Sla::factory()->high()->create([
            'resolution_time_minutes' => 120,
        ]);

        $response = $this->postJson('/api/v1/tickets', [
            'title' => 'Production outage',
            'description' => 'API is unavailable.',
            'priority' => 'high',
            'category_id' => $category->id,
            'customer_id' => $customer->id,
        ])->assertCreated();

        $this->assertEquals(
            now()->addMinutes(120)->startOfSecond(),
            Carbon::parse($response->json('data.sla_due_date'))->startOfSecond()
        );
    }

    public function test_ticket_requires_title_description_category_and_customer(): void
    {
        $this->postJson('/api/v1/tickets', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'title',
                'description',
                'category_id',
                'customer_id',
            ]);
    }

    public function test_priority_must_be_valid(): void
    {
        $category = Category::factory()->create();
        $customer = User::factory()->customer()->create();

        $this->postJson('/api/v1/tickets', [
            'title' => 'Invalid priority',
            'description' => 'Should fail validation.',
            'priority' => 'urgent',
            'category_id' => $category->id,
            'customer_id' => $customer->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('priority');
    }

    public function test_customer_must_have_customer_role(): void
    {
        $category = Category::factory()->create();
        $agent = User::factory()->agent()->create();
        Sla::factory()->medium()->create();

        $this->postJson('/api/v1/tickets', [
            'title' => 'Invalid customer',
            'description' => 'Agent cannot be the customer.',
            'category_id' => $category->id,
            'customer_id' => $agent->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('customer_id');
    }

    public function test_assignee_must_have_agent_role(): void
    {
        $category = Category::factory()->create();
        $customer = User::factory()->customer()->create();
        Sla::factory()->medium()->create();

        $this->postJson('/api/v1/tickets', [
            'title' => 'Invalid assignee',
            'description' => 'Customer cannot be assigned.',
            'category_id' => $category->id,
            'customer_id' => $customer->id,
            'assigned_to' => $customer->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('assigned_to');
    }

    public function test_cannot_create_ticket_without_active_sla_for_priority(): void
    {
        $category = Category::factory()->create();
        $customer = User::factory()->customer()->create();

        $this->postJson('/api/v1/tickets', [
            'title' => 'No SLA',
            'description' => 'Critical tickets are not configured.',
            'priority' => 'critical',
            'category_id' => $category->id,
            'customer_id' => $customer->id,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('priority');
    }

    public function test_can_update_ticket(): void
    {
        $ticket = Ticket::factory()->create();
        $category = Category::factory()->create();
        $agent = User::factory()->agent()->create();
        $sla = Sla::factory()->high()->create();

        $payload = [
            'title' => 'Updated title',
            'description' => 'Updated description.',
            'status' => 'in_progress',
            'priority' => 'high',
            'category_id' => $category->id,
            'assigned_to' => $agent->id,
        ];

        $this->putJson(
            "/api/v1/tickets/{$ticket->id}",
            $payload
        )
            ->assertOk()
            ->assertJsonPath('data.id', $ticket->id)
            ->assertJsonPath('data.title', 'Updated title')
            ->assertJsonPath('data.description', 'Updated description.')
            ->assertJsonPath('data.status', 'in_progress')
            ->assertJsonPath('data.priority', 'high')
            ->assertJsonPath('data.category_id', $category->id)
            ->assertJsonPath('data.assigned_to', $agent->id)
            ->assertJsonPath('data.sla_id', $sla->id);

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'title' => 'Updated title',
            'status' => 'in_progress',
            'priority' => 'high',
            'assigned_to' => $agent->id,
            'sla_id' => $sla->id,
        ]);
    }

    public function test_resolving_ticket_sets_resolved_at(): void
    {
        $this->freezeTime();

        $ticket = Ticket::factory()->create();

        $response = $this->putJson("/api/v1/tickets/{$ticket->id}", [
            'title' => $ticket->title,
            'description' => $ticket->description,
            'status' => 'resolved',
            'priority' => $ticket->priority->value,
            'category_id' => $ticket->category_id,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', 'resolved');

        $this->assertEquals(
            now()->startOfSecond(),
            Carbon::parse($response->json('data.resolved_at'))->startOfSecond()
        );
    }

    public function test_closing_ticket_sets_closed_at(): void
    {
        $this->freezeTime();

        $ticket = Ticket::factory()->create();

        $response = $this->putJson("/api/v1/tickets/{$ticket->id}", [
            'title' => $ticket->title,
            'description' => $ticket->description,
            'status' => 'closed',
            'priority' => $ticket->priority->value,
            'category_id' => $ticket->category_id,
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('data.status', 'closed');

        $this->assertEquals(
            now()->startOfSecond(),
            Carbon::parse($response->json('data.closed_at'))->startOfSecond()
        );
    }

    public function test_returns_404_when_updating_nonexistent_ticket(): void
    {
        $category = Category::factory()->create();

        $this->putJson('/api/v1/tickets/999999', [
            'title' => 'Missing',
            'description' => 'Does not exist.',
            'status' => 'open',
            'priority' => 'medium',
            'category_id' => $category->id,
        ])
            ->assertNotFound();
    }

    public function test_can_delete_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $this->deleteJson("/api/v1/tickets/{$ticket->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
        ]);
    }

    public function test_returns_404_when_deleting_nonexistent_ticket(): void
    {
        $this->deleteJson('/api/v1/tickets/999999')
            ->assertNotFound();
    }
}
