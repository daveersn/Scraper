<?php

namespace App\Actions\Targets;

use App\Enums\ItemStatus;
use App\Jobs\UpdateGoneItemChunkJob;
use App\Models\Target;
use App\Scraping\Drivers\Contracts\ChecksItemExistence;
use App\Scraping\Drivers\ScraperDriver;
use App\Scraping\ScraperRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateGoneItems
{
    use AsAction;

    public $commandSignature = 'target:update-gone-items';

    public $commandDescription = 'Checks if all Active items still exists. If not, set its status to Gone.';

    public function __construct(
        private readonly ScraperRegistry $registry
    ) {}

    public function handle(): void
    {
        $drivers = collect($this->registry->getDrivers());

        // Get all drivers which can check item existence
        $drivers = $drivers
            ->map(function (ScraperDriver $driver) {
                if (! ($driver instanceof ChecksItemExistence)) {
                    return null;
                }

                return $driver::getDriverType();
            })
            ->filter()
            ->all();

        // Fetch all targets that have valid drivers, and load currently active items
        $targets = Target::query()
            ->whereIn('driver', $drivers)
            ->with('items',
                fn (Builder|BelongsToMany $query) => $query->where('items.status', ItemStatus::ACTIVE)
            )
            ->get();

        $targets->each(function (Target $target) {
            $target->items
                ->chunk(25)
                ->each(
                    fn (Collection $chunk) => UpdateGoneItemChunkJob::dispatch($chunk, $target->driver)
                );
        });
    }
}
