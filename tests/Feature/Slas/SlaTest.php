<?php

namespace Tests\Feature\Slas;

use App\Enums\Priority;
use App\Models\Sla;
use App\Services\SlaService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SlaTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_slas(): void
    {
        Sla::factory()->low()->create();
        Sla::factory()->medium()->create();
        Sla::factory()->high()->create();

        $this->getJson('/api/v1/slas')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_view_sla(): void
    {
        $sla = Sla::factory()->high()->create();

        $this->getJson("/api/v1/slas/{$sla->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $sla->id)
            ->assertJsonPath('data.uuid', $sla->uuid)
            ->assertJsonPath('data.name', $sla->name)
            ->assertJsonPath('data.priority', 'high')
            ->assertJsonPath(
                'data.response_time_minutes',
                $sla->response_time_minutes
            )
            ->assertJsonPath(
                'data.resolution_time_minutes',
                $sla->resolution_time_minutes
            )
            ->assertJsonPath('data.active', true);
    }

    public function test_returns_404_for_nonexistent_sla(): void
    {
        $this->getJson('/api/v1/slas/999999')
            ->assertNotFound();
    }

    public function test_can_create_sla(): void
    {
        $payload = [
            'name' => 'SLA Alta Prioridade',
            'priority' => 'high',
            'response_time_minutes' => 60,
            'resolution_time_minutes' => 480,
            'active' => true,
        ];

        $this->postJson('/api/v1/slas', $payload)
            ->assertCreated()
            ->assertJsonPath('data.name', 'SLA Alta Prioridade')
            ->assertJsonPath('data.priority', 'high')
            ->assertJsonPath('data.response_time_minutes', 60)
            ->assertJsonPath('data.resolution_time_minutes', 480)
            ->assertJsonPath('data.active', true);

        $this->assertDatabaseHas('slas', [
            'name' => 'SLA Alta Prioridade',
            'priority' => 'high',
            'response_time_minutes' => 60,
            'resolution_time_minutes' => 480,
        ]);
    }

    public function test_sla_generates_uuid_automatically(): void
    {
        $sla = Sla::factory()->low()->create();

        $this->assertNotNull($sla->uuid);

        $this->assertDatabaseHas('slas', [
            'id' => $sla->id,
            'uuid' => $sla->uuid,
        ]);
    }

    public function test_sla_requires_name(): void
    {
        $this->postJson('/api/v1/slas', [
            'priority' => 'high',
            'response_time_minutes' => 60,
            'resolution_time_minutes' => 480,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_sla_requires_priority(): void
    {
        $this->postJson('/api/v1/slas', [
            'name' => 'SLA',
            'response_time_minutes' => 60,
            'resolution_time_minutes' => 480,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('priority');
    }

    public function test_sla_requires_response_time(): void
    {
        $this->postJson('/api/v1/slas', [
            'name' => 'SLA',
            'priority' => 'high',
            'resolution_time_minutes' => 480,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(
                'response_time_minutes'
            );
    }

    public function test_sla_requires_resolution_time(): void
    {
        $this->postJson('/api/v1/slas', [
            'name' => 'SLA',
            'priority' => 'high',
            'response_time_minutes' => 60,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(
                'resolution_time_minutes'
            );
    }

    public function test_priority_must_be_valid(): void
    {
        $this->postJson('/api/v1/slas', [
            'name' => 'Invalid SLA',
            'priority' => 'invalid',
            'response_time_minutes' => 60,
            'resolution_time_minutes' => 480,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('priority');
    }

    public function test_response_time_must_be_at_least_one_minute(): void
    {
        $this->postJson('/api/v1/slas', [
            'name' => 'Invalid SLA',
            'priority' => 'high',
            'response_time_minutes' => 0,
            'resolution_time_minutes' => 480,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(
                'response_time_minutes'
            );
    }

    public function test_resolution_time_must_be_at_least_one_minute(): void
    {
        $this->postJson('/api/v1/slas', [
            'name' => 'Invalid SLA',
            'priority' => 'high',
            'response_time_minutes' => 60,
            'resolution_time_minutes' => 0,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(
                'resolution_time_minutes'
            );
    }

    public function test_sla_name_must_be_unique(): void
    {
        Sla::factory()->low()->create([
            'name' => 'Support SLA',
        ]);

        $this->postJson('/api/v1/slas', [
            'name' => 'Support SLA',
            'priority' => 'high',
            'response_time_minutes' => 60,
            'resolution_time_minutes' => 480,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_sla_priority_must_be_unique(): void
    {
        Sla::factory()->low()->create();

        $this->postJson('/api/v1/slas', [
            'name' => 'Another Low SLA',
            'priority' => 'low',
            'response_time_minutes' => 120,
            'resolution_time_minutes' => 600,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('priority');
    }

    public function test_can_update_sla(): void
    {
        $sla = Sla::factory()->high()->create();

        $payload = [
            'name' => 'Updated SLA',
            'priority' => 'critical',
            'response_time_minutes' => 15,
            'resolution_time_minutes' => 120,
            'active' => false,
        ];

        $this->putJson(
            "/api/v1/slas/{$sla->id}",
            $payload
        )
            ->assertOk()
            ->assertJsonPath('data.id', $sla->id)
            ->assertJsonPath('data.name', 'Updated SLA')
            ->assertJsonPath('data.priority', 'critical')
            ->assertJsonPath(
                'data.response_time_minutes',
                15
            )
            ->assertJsonPath(
                'data.resolution_time_minutes',
                120
            )
            ->assertJsonPath('data.active', false);

        $this->assertDatabaseHas('slas', [
            'id' => $sla->id,
            'name' => 'Updated SLA',
            'priority' => 'critical',
            'response_time_minutes' => 15,
            'resolution_time_minutes' => 120,
            'active' => false,
        ]);
    }

    public function test_can_update_sla_without_changing_unique_fields(): void
    {
        $sla = Sla::factory()->high()->create([
            'name' => 'High SLA',
            'priority' => Priority::HIGH,
        ]);

        $this->putJson(
            "/api/v1/slas/{$sla->id}",
            [
                'name' => 'High SLA',
                'priority' => 'high',
                'response_time_minutes' => 90,
                'resolution_time_minutes' => 600,
            ]
        )
            ->assertOk()
            ->assertJsonPath('data.name', 'High SLA')
            ->assertJsonPath('data.priority', 'high')
            ->assertJsonPath(
                'data.response_time_minutes',
                90
            );
    }

    public function test_cannot_update_sla_with_duplicate_name(): void
    {
        $sla = Sla::factory()->low()->create([
            'name' => 'Low SLA',
        ]);

        Sla::factory()->medium()->create([
            'name' => 'Medium SLA',
        ]);

        $this->putJson(
            "/api/v1/slas/{$sla->id}",
            [
                'name' => 'Medium SLA',
                'priority' => 'low',
                'response_time_minutes' => 480,
                'resolution_time_minutes' => 2880,
            ]
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_cannot_update_sla_with_duplicate_priority(): void
    {
        $sla = Sla::factory()->low()->create();

        Sla::factory()->medium()->create();

        $this->putJson(
            "/api/v1/slas/{$sla->id}",
            [
                'name' => 'Updated Low SLA',
                'priority' => 'medium',
                'response_time_minutes' => 480,
                'resolution_time_minutes' => 2880,
            ]
        )
            ->assertUnprocessable()
            ->assertJsonValidationErrors('priority');
    }

    public function test_returns_404_when_updating_nonexistent_sla(): void
    {
        $this->putJson('/api/v1/slas/999999', [
            'name' => 'SLA',
            'priority' => 'high',
            'response_time_minutes' => 60,
            'resolution_time_minutes' => 480,
        ])
            ->assertNotFound();
    }

    public function test_can_delete_sla(): void
    {
        $sla = Sla::factory()->low()->create();

        $this->deleteJson("/api/v1/slas/{$sla->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('slas', [
            'id' => $sla->id,
        ]);
    }

    public function test_returns_404_when_deleting_nonexistent_sla(): void
    {
        $this->deleteJson('/api/v1/slas/999999')
            ->assertNotFound();
    }

    public function test_finds_active_sla_by_priority(): void
    {
        $sla = Sla::factory()->high()->create();

        $service = app(SlaService::class);

        $result = $service->findForPriority(Priority::HIGH);

        $this->assertTrue($result->is($sla));
    }

    public function test_does_not_find_inactive_sla_by_priority(): void
    {
        Sla::factory()
            ->high()
            ->inactive()
            ->create();

        $service = app(SlaService::class);

        $this->expectException(
            \Illuminate\Database\Eloquent\ModelNotFoundException::class
        );

        $service->findForPriority(Priority::HIGH);
    }

    public function test_calculates_response_deadline(): void
    {
        $sla = Sla::factory()->high()->create([
            'response_time_minutes' => 60,
        ]);

        $start = CarbonImmutable::parse(
            '2026-09-28 10:00:00'
        );

        $service = app(SlaService::class);

        $deadline = $service->calculateResponseDeadline(
            $sla,
            $start
        );

        $this->assertSame(
            '2026-09-28 11:00:00',
            $deadline->format('Y-m-d H:i:s')
        );
    }

    public function test_calculates_resolution_deadline(): void
    {
        $sla = Sla::factory()->high()->create([
            'resolution_time_minutes' => 480,
        ]);

        $start = CarbonImmutable::parse(
            '2026-09-28 10:00:00'
        );

        $service = app(SlaService::class);

        $deadline = $service->calculateResolutionDeadline(
            $sla,
            $start
        );

        $this->assertSame(
            '2026-09-28 18:00:00',
            $deadline->format('Y-m-d H:i:s')
        );
    }
}
