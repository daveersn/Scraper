<?php

namespace App\Scraping\Drivers;

use App\Actions\Drivers\Subito\ScrapePage;
use App\Actions\Drivers\Subito\VerifyItemExistence;
use App\DTO\ExtraFields\SubitoExtraFields;
use App\DTO\ScrapedItemData;
use App\DTO\ScrapeRequestData;
use App\DTO\SubitoItem;
use App\Enums\ScraperDriverType;
use App\Filament\Imports\SubitoItemImporter;
use App\Http\Integrations\Browser\Browser;
use App\Scraping\Drivers\Contracts\ChecksItemExistence;
use App\Support\BlueprintInterpreter;
use HeadlessChromium\Page;

class SubitoScraperDriver extends ScraperDriver implements ChecksItemExistence
{
    public function __construct(
        protected BlueprintInterpreter $interpreter,
    ) {}

    public function canHandle(ScrapeRequestData $request): bool
    {
        return strtolower(parse_domain($request->url)) === 'subito.it';
    }

    /**
     * @return array<int, ScrapedItemData>
     */
    public function fetchItems(ScrapeRequestData $request): array
    {
        $browser = app(Browser::class);
        $extraFieldsClass = self::getExtraFieldsClass();

        return $browser->wrapInPage(fn (Page $page) => ScrapePage::run($page, $request->url)
            ->map(fn (SubitoItem $item) => new ScrapedItemData(
                url: $item->link,
                title: $item->title,
                externalId: $item->item_id,
                price: $item->price,
                currency: config('app.currency'),
                extraFields: new $extraFieldsClass(
                    town: $item->town,
                    uploadedDateTime: $item->uploadedDateTime,
                    status: $item->status,
                )
            ))
            ->all());
    }

    public function itemExists(ScrapeRequestData $request): bool
    {
        return VerifyItemExistence::run($request);
    }

    public static function getExtraFieldsClass(): ?string
    {
        return SubitoExtraFields::class;
    }

    public static function getDriverType(): ScraperDriverType
    {
        return ScraperDriverType::Subito;
    }

    public static function getImporterClass(): ?string
    {
        return SubitoItemImporter::class;
    }
}
