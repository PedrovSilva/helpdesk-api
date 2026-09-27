<?php

namespace App\Models;

use App\Enums\Priority;
use Database\Factories\SlasFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'priority','response_time','resolution_time', 'is_active'])]
class Slas extends Model
{
    /** @use HasFactory<SlasFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'priority' => Priority::class];
    }

    public function tickets(): hasMany
    {
        return $this->hasMany(Slas::class);
    }
}
