<?php

namespace Tests\Feature\Comments;

use App\Models\Comment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_comments(): void
    {
        Comment::factory()->count(3)->create();

        $this->getJson('/api/v1/comments')
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_can_view_comment(): void
    {
        $comment = Comment::factory()->create();

        $this->getJson("/api/v1/comments/{$comment->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $comment->id)
            ->assertJsonPath('data.ticket_id', $comment->ticket_id)
            ->assertJsonPath('data.user_id', $comment->user_id)
            ->assertJsonPath('data.body', $comment->body);
    }

    public function test_returns_404_for_nonexistent_comment(): void
    {
        $this->getJson('/api/v1/comments/999999')
            ->assertNotFound();
    }

    public function test_can_create_comment(): void
    {
        $ticket = Ticket::factory()->create();
        $user = User::factory()->create();

        $payload = [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => 'Please restart the workstation and try again.',
        ];

        $this->postJson('/api/v1/comments', $payload)
            ->assertCreated()
            ->assertJsonPath('data.ticket_id', $ticket->id)
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath(
                'data.body',
                'Please restart the workstation and try again.'
            );

        $this->assertDatabaseHas('comments', [
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'body' => 'Please restart the workstation and try again.',
        ]);
    }

    public function test_comment_requires_ticket_user_and_body(): void
    {
        $this->postJson('/api/v1/comments', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                'ticket_id',
                'user_id',
                'body',
            ]);
    }

    public function test_ticket_must_exist(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/v1/comments', [
            'ticket_id' => 999999,
            'user_id' => $user->id,
            'body' => 'Missing ticket.',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('ticket_id');
    }

    public function test_user_must_exist(): void
    {
        $ticket = Ticket::factory()->create();

        $this->postJson('/api/v1/comments', [
            'ticket_id' => $ticket->id,
            'user_id' => 999999,
            'body' => 'Missing user.',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('user_id');
    }

    public function test_can_update_comment(): void
    {
        $comment = Comment::factory()->create();

        $this->putJson("/api/v1/comments/{$comment->id}", [
            'body' => 'Updated comment body.',
        ])
            ->assertOk()
            ->assertJsonPath('data.id', $comment->id)
            ->assertJsonPath('data.body', 'Updated comment body.');

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'body' => 'Updated comment body.',
        ]);
    }

    public function test_returns_404_when_updating_nonexistent_comment(): void
    {
        $this->putJson('/api/v1/comments/999999', [
            'body' => 'Missing comment.',
        ])
            ->assertNotFound();
    }

    public function test_can_delete_comment(): void
    {
        $comment = Comment::factory()->create();

        $this->deleteJson("/api/v1/comments/{$comment->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_returns_404_when_deleting_nonexistent_comment(): void
    {
        $this->deleteJson('/api/v1/comments/999999')
            ->assertNotFound();
    }
}
