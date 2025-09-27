<?php

namespace App\Scraping\Drivers;

use App\Actions\Drivers\Subito\ScrapePage;
use App\Browser\Browser;
use App\DTO\ScrapedItemData;
use App\DTO\ScrapeRequestData;
use App\DTO\SubitoExtraFields;
use App\DTO\SubitoItem;
use App\Support\BlueprintInterpreter;
use HeadlessChromium\Page;

class SubitoScraperDriver extends ScraperDriver
{
    public function __construct(
        protected Browser $browser,
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
        return $this->browser->wrapInPage(fn (Page $page) => ScrapePage::run($page, $request->url)
            ->map(fn (SubitoItem $item) => new ScrapedItemData(
                url: $item->link,
                title: $item->title,
                externalId: $item->item_id,
                price: $item->price,
                currency: 'EUR',
                extraFields: new SubitoExtraFields(
                    town: $item->town,
                    uploadedDateTime: $item->uploadedDateTime,
                    status: $item->status,
                )
            ))
            ->all());
    }
}
