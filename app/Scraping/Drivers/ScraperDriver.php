<?php

namespace App\Scraping\Drivers;

use App\DTO\ScrapedItemData;
use App\DTO\ScrapeRequestData;

abstract class ScraperDriver
{
    /**
     * Determine if this driver can handle the given request.
     */
    abstract public function canHandle(ScrapeRequestData $request): bool;

    /**
     * Fetch and extract items for the request.
     *
     * @return array<int, ScrapedItemData>
     */
    abstract public function fetchItems(ScrapeRequestData $request): array;
}
