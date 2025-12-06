<?php

namespace App\Actions\Targets;

use App\DTO\ScrapeRequestData;
use App\Enums\ItemStatus;
use App\Models\Item;
use App\Models\Target;
use App\Scraping\Drivers\Contracts\ChecksItemExistence;
use App\Scraping\Drivers\ScraperDriver;
use App\Scraping\ScraperRegistry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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

        $drivers = $drivers
            ->map(function (ScraperDriver $driver) {
                if (! ($driver instanceof ChecksItemExistence)) {
                    return null;
                }

                return $driver::getDriverType();
            })
            ->filter()
            ->all();

        $targets = Target::query()
            ->whereIn('driver', $drivers)
            ->with('items',
                fn (Builder|BelongsToMany $query) => $query->where('items.status', ItemStatus::ACTIVE)
            )
            ->get();

        $targets->each(function (Target $target) {
            /** @var ChecksItemExistence $driver */
            $driver = $this->registry->resolveFromType($target->driver);

            $target->items->each(function (Item $item) use ($driver) {
                $exists = $driver->itemExists(new ScrapeRequestData(url: $item->url));

                if ($exists) {
                    return;
                }

                $item->update([
                    'status' => ItemStatus::GONE,
                ]);
            });
        });
    }
}
