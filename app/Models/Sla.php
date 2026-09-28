<?php

namespace App\Models;

use App\Enums\Priority;
use Database\Factories\SlaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'priority','response_time','resolution_time', 'is_active'])]
class Sla extends Model
{
    /** @use HasFactory<SlaFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(fn ($slas) => $slas->uuid = (string) Str::uuid());
    }
    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'priority' => Priority::class];
    }

    public function tickets(): hasMany
    {
        return $this->hasMany(Sla::class);
    }
}
