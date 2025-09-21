<?php

namespace App\Scraping;

use App\Scraping\Contracts\ScraperDriver;
use App\Scraping\DTO\ScrapeRequestData;

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
}
