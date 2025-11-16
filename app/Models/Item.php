<?php

namespace App\Models;

use App\Casts\ExtraFieldsCast;
use App\Enums\ItemStatus;
use App\Observers\ItemObserver;
use Cknow\Money\Casts\MoneyIntegerCast;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[ObservedBy([ItemObserver::class])]
class Item extends Model
{
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'current_price' => MoneyIntegerCast::class,
        'extra_fields' => ExtraFieldsCast::class,
        'status' => ItemStatus::class,
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

    public function latestPrice(): HasOne
    {
        return $this->hasOne(ItemPrice::class)->latestOfMany();
    }

    /**
     * @return BelongsToMany<Target>
     */
    public function targets(): BelongsToMany
    {
        return $this->belongsToMany(Target::class)
            ->withPivot(['first_seen_at', 'last_seen_at'])
            ->withTimestamps();
    }
}
