<?php

namespace App\Scraping\Drivers\Subito;

use App\Scraping\Contracts\ScraperDriver;
use App\Scraping\Drivers\Subito\Actions\ScrapePage;
use App\Scraping\DTO\ScrapedItemData;
use App\Scraping\DTO\ScrapeRequestData;
use App\Scraping\Support\BlueprintInterpreter;

class SubitoScraperDriver implements ScraperDriver
{
    public function __construct(
        protected ?BlueprintInterpreter $interpreter = null,
    ) {
        $this->interpreter ??= new BlueprintInterpreter;
    }

    public function canHandle(ScrapeRequestData $request): bool
    {
        return strtolower(parse_domain($request->url)) === 'subito.it';
    }

    /**
     * @return array<int, ScrapedItemData>
     */
    public function fetchItems(ScrapeRequestData $request): array
    {
        ScrapePage::run();
    }
}
