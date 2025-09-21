<?php

namespace App\Scraping\Contracts;

use App\Scraping\DTO\ScrapedItemData;
use App\Scraping\DTO\ScrapeRequestData;

interface ScraperDriver
{
    /**
     * Determine if this driver can handle the given request.
     */
    public function canHandle(ScrapeRequestData $request): bool;

    /**
     * Fetch and extract items for the request.
     *
     * @return array<int, ScrapedItemData>
     */
    public function fetchItems(ScrapeRequestData $request): array;
}
