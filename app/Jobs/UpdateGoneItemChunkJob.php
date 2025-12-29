<?php

namespace App\Jobs;

use App\DTO\ScrapeRequestData;
use App\Enums\ItemStatus;
use App\Enums\Queue;
use App\Enums\ScraperDriverType;
use App\Models\Item;
use App\Scraping\Drivers\Contracts\ChecksItemExistence;
use App\Scraping\ScraperRegistry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Collection;

class UpdateGoneItemChunkJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public readonly Collection $chunk,
        public readonly ScraperDriverType $driverType,
    ) {
        $this->onQueue(Queue::BROWSER->value);
    }

    public function handle(ScraperRegistry $registry): void
    {
        /** @var ChecksItemExistence $driver */
        $driver = $registry->resolveFromType($this->driverType);

        $this->chunk->each(function (Item $item) use ($driver) {
            if ($item->status === ItemStatus::GONE) {
                return;
            }

            $exists = $driver->itemExists(new ScrapeRequestData(url: $item->url));

            if ($exists) {
                return;
            }

            $item->update([
                'status' => ItemStatus::GONE,
            ]);
        });
    }
}
