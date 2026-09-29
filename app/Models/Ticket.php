<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['ticket_number', 'title', 'description', 'status', 'priority', 'category_id', 'sla_id', 'customer_id', 'assigned_to', 'sla_due_date', 'resolved_at', 'closed_at'])]
class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
            'priority' => Priority::class,
            'sla_due_date' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            $ticket->ticket_number ??= (string) Str::uuid();
        });
    }

    /**
     * @param  Builder<Ticket>  $query
     * @param  array<string, mixed>  $filters
     */
    public function scopeFiltered(Builder $query, array $filters): void
    {
        $query
            ->when(
                array_key_exists('status', $filters),
                fn (Builder $query) => $query->where('status', $filters['status'])
            )
            ->when(
                array_key_exists('priority', $filters),
                fn (Builder $query) => $query->where('priority', $filters['priority'])
            )
            ->when(
                array_key_exists('category_id', $filters),
                fn (Builder $query) => $query->where('category_id', $filters['category_id'])
            )
            ->when(
                array_key_exists('customer_id', $filters),
                fn (Builder $query) => $query->where('customer_id', $filters['customer_id'])
            )
            ->when(
                array_key_exists('assigned_to', $filters),
                fn (Builder $query) => $query->where('assigned_to', $filters['assigned_to'])
            )
            ->when(
                array_key_exists('sla_id', $filters),
                fn (Builder $query) => $query->where('sla_id', $filters['sla_id'])
            );
    }

    public function sla(): BelongsTo
    {
        return $this->belongsTo(Sla::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(TicketHistory::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id')->where('user_role', UserRole::CUSTOMER);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to')->where('user_role', UserRole::AGENT);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }
}
