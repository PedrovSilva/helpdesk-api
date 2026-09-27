<?php

namespace App\Models;

use Database\Factories\TicketHistoriesFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['ticket_id', 'user_id','event','from_value','to_value','metadata'])]
class TicketHistories extends Model
{
    /** @use HasFactory<TicketHistoriesFactory> */
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Tickets::class);
    }
}
