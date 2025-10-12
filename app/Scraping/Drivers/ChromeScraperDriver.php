<?php

namespace App\Scraping\Drivers;

use App\DTO\ScrapedItemData;
use App\DTO\ScrapeRequestData;
use App\Enums\ScraperDriverType;
use App\Support\BlueprintInterpreter;

class ChromeScraperDriver extends ScraperDriver
{
    public function __construct(
        protected ?BlueprintInterpreter $interpreter = null,
    ) {
        $this->interpreter ??= new BlueprintInterpreter;
    }

    public function canHandle(ScrapeRequestData $request): bool
    {
        return false;
    }

    /**
     * @return array<int, ScrapedItemData>
     */
    public function fetchItems(ScrapeRequestData $request): array
    {
        return [];
    }

    public static function getDriverType(): ScraperDriverType
    {
        return ScraperDriverType::Chrome;
    }
}
