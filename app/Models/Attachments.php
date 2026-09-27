<?php

namespace App\Models;

use Database\Factories\AttachmentsFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[fillable(['ticket_id','file_path', 'file_name', 'mime_type', 'file_size'])]
class Attachments extends Model
{
    /** @use HasFactory<AttachmentsFactory> */
    use HasFactory;

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Tickets::class);
    }
}
