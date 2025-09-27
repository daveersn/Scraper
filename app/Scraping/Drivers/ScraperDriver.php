<?php

namespace App\Scraping\Drivers;

use App\DTO\ScrapedItemData;
use App\DTO\ScrapeRequestData;

abstract class ScraperDriver
{
    abstract public function canHandle(ScrapeRequestData $request): bool;

    /**
     * @return array<int, ScrapedItemData>
     */
    abstract public function fetchItems(ScrapeRequestData $request): array;
}
