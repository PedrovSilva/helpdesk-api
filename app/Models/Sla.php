<?php

namespace App\Models;

use App\Enums\Priority;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sla extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'priority',
        'response_time_minutes',
        'resolution_time_minutes',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'priority' => Priority::class,
            'active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Sla $sla) {
            $sla->uuid ??= (string) Str::uuid();
        });
    }
}
