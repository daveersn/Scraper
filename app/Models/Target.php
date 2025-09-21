<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Target extends Model
{
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'blueprint' => 'array',
        'active' => 'bool',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, Target>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany<Item>
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)
            ->withPivot(['first_seen_at', 'last_seen_at'])
            ->withTimestamps();
    }
}
