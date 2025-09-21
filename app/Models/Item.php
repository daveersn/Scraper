<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'first_seen_at' => 'datetime',
        'last_seen_at' => 'datetime',
    ];

    /**
     * @return HasMany<ItemPrice>
     */
    public function prices(): HasMany
    {
        return $this->hasMany(ItemPrice::class);
    }

    /**
     * @return BelongsToMany<Target>
     */
    public function targets(): BelongsToMany
    {
        return $this->belongsToMany(Target::class, 'target_item')
            ->withPivot(['first_seen_at', 'last_seen_at'])
            ->withTimestamps();
    }
}
