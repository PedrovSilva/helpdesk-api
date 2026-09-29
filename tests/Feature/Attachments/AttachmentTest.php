<?php

namespace Tests\Feature\Attachments;

use App\Models\Attachment;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttachmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_attachments(): void
    {
        Attachment::factory()->count(3)->create();

        $this->getJson('/api/v1/attachments')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_view_attachment(): void
    {
        $attachment = Attachment::factory()->create();

        $this->getJson("/api/v1/attachments/{$attachment->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $attachment->id)
            ->assertJsonPath('data.ticket_id', $attachment->ticket_id)
            ->assertJsonPath('data.file_path', $attachment->file_path)
            ->assertJsonPath('data.file_name', $attachment->file_name)
            ->assertJsonPath('data.mime_type', $attachment->mime_type)
            ->assertJsonPath('data.file_size', $attachment->file_size);
    }

    public function test_returns_404_for_nonexistent_attachment(): void
    {
        $this->getJson('/api/v1/attachments/999999')
            ->assertNotFound();
    }

    public function test_can_create_attachment(): void
    {
        $ticket = Ticket::factory()->create();

        $payload = [
            'ticket_id' => $ticket->id,
            'file_path' => 'attachments/screenshot.png',
            'file_name' => 'screenshot.png',
            'mime_type' => 'image/png',
            'file_size' => 2048,
        ];

        $this->postJson('/api/v1/attachments', $payload)
            ->assertCreated()
            ->assertJsonPath('data.ticket_id', $ticket->id)
            ->assertJsonPath('data.file_path', 'attachments/screenshot.png')
            ->assertJsonPath('data.file_name', 'screenshot.png')
            ->assertJsonPath('data.mime_type', 'image/png')
            ->assertJsonPath('data.file_size', 2048);

        $this->assertDatabaseHas('attachments', [
            'ticket_id' => $ticket->id,
            'file_path' => 'attachments/screenshot.png',
            'file_name' => 'screenshot.png',
            'mime_type' => 'image/png',
            'file_size' => 2048,
        ]);
    }

    public function test_attachment_requires_ticket_and_file_fields(): void
    {
        $this->postJson('/api/v1/attachments', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'ticket_id',
                'file_path',
                'file_name',
                'mime_type',
                'file_size',
            ]);
    }

    public function test_ticket_must_exist(): void
    {
        $this->postJson('/api/v1/attachments', [
            'ticket_id' => 999999,
            'file_path' => 'attachments/missing.png',
            'file_name' => 'missing.png',
            'mime_type' => 'image/png',
            'file_size' => 1024,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('ticket_id');
    }

    public function test_file_size_must_be_at_least_one(): void
    {
        $ticket = Ticket::factory()->create();

        $this->postJson('/api/v1/attachments', [
            'ticket_id' => $ticket->id,
            'file_path' => 'attachments/empty.txt',
            'file_name' => 'empty.txt',
            'mime_type' => 'text/plain',
            'file_size' => 0,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('file_size');
    }

    public function test_can_update_attachment(): void
    {
        $attachment = Attachment::factory()->create();

        $payload = [
            'file_path' => 'attachments/updated.pdf',
            'file_name' => 'updated.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 4096,
        ];

        $this->putJson("/api/v1/attachments/{$attachment->id}", $payload)
            ->assertOk()
            ->assertJsonPath('data.id', $attachment->id)
            ->assertJsonPath('data.file_path', 'attachments/updated.pdf')
            ->assertJsonPath('data.file_name', 'updated.pdf')
            ->assertJsonPath('data.mime_type', 'application/pdf')
            ->assertJsonPath('data.file_size', 4096);

        $this->assertDatabaseHas('attachments', [
            'id' => $attachment->id,
            'file_path' => 'attachments/updated.pdf',
            'file_name' => 'updated.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 4096,
        ]);
    }

    public function test_returns_404_when_updating_nonexistent_attachment(): void
    {
        $this->putJson('/api/v1/attachments/999999', [
            'file_path' => 'attachments/missing.pdf',
            'file_name' => 'missing.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
        ])
            ->assertNotFound();
    }

    public function test_can_delete_attachment(): void
    {
        $attachment = Attachment::factory()->create();

        $this->deleteJson("/api/v1/attachments/{$attachment->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('attachments', [
            'id' => $attachment->id,
        ]);
    }

    public function test_returns_404_when_deleting_nonexistent_attachment(): void
    {
        $this->deleteJson('/api/v1/attachments/999999')
            ->assertNotFound();
    }
}
