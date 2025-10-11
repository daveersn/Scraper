<?php

namespace App\Scraping;

use App\DTO\ScrapeRequestData;
use App\Enums\ScraperDriverType;
use App\Scraping\Drivers\ScraperDriver;

class ScraperRegistry
{
    /**
     * @param  iterable<int, ScraperDriver>  $drivers
     */
    public function __construct(
        protected iterable $drivers,
    ) {}

    public function resolveFor(ScrapeRequestData $request): ?ScraperDriver
    {
        foreach ($this->drivers as $driver) {
            if ($driver->canHandle($request)) {
                return $driver;
            }
        }

        return null;
    }

    public function resolveFromType(ScraperDriverType $type): ?ScraperDriver
    {
        foreach ($this->drivers as $driver) {
            if ($driver::getDriverType() === $type) {
                return $driver;
            }
        }

        return null;
    }
}
