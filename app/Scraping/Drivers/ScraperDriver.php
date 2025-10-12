<?php

namespace App\Scraping\Drivers;

use App\DTO\ExtraFields\ExtraFields;
use App\DTO\ScrapedItemData;
use App\DTO\ScrapeRequestData;
use App\Enums\ScraperDriverType;

abstract class ScraperDriver
{
    abstract public function canHandle(ScrapeRequestData $request): bool;

    /**
     * @return array<int, ScrapedItemData>
     */
    abstract public function fetchItems(ScrapeRequestData $request): array;

    abstract public static function getDriverType(): ScraperDriverType;

    /** @return class-string<ExtraFields>|null */
    public static function getExtraFieldsClass(): ?string
    {
        return null;
    }

    /** @return class-string<ExtraFields>|null */
    public static function getImporterClass(): ?string
    {
        return null;
    }
}
