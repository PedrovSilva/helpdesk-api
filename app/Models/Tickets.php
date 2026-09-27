<?php

namespace App\Models;

use App\Enums\Priority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use Database\Factories\TicketsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['ticket_number','title', 'description', 'status', 'priority', 'category_id', 'sla_id', 'customer_id'])]
class Tickets extends Model
{
    /** @use HasFactory<TicketsFactory> */
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

    public function slas(): BelongsTo
    {
        return $this->belongsTo(Slas::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Categories::class);
    }
    public function history(): HasMany
    {
        return $this->hasMany(TicketHistories::class);
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id')->where('user_role', UserRole::COSTUMER);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to')->where('user_role', UserRole::AGENT);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comments::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachments::class);
    }
}
