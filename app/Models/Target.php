<?php

namespace App\Models;

use App\DTO\ScrapeRequestData;
use App\Enums\ScraperDriverType;
use App\Models\Scopes\ActiveScope;
use App\Observers\TargetObserver;
use App\Scraping\Drivers\ScraperDriver;
use App\Scraping\ScraperRegistry;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([TargetObserver::class])]
#[ScopedBy(ActiveScope::class)]
class Target extends Model
{
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'blueprint' => 'array',
        'active' => 'bool',
        'driver' => ScraperDriverType::class,
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

    public function getScraperDriver(): ?ScraperDriver
    {
        $registry = app(ScraperRegistry::class);

        return $this->driver !== null
            ? $registry->resolveFromType($this->driver)
            : $registry->resolveFor(ScrapeRequestData::from($this));
    }
}
