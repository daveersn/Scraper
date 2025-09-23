<?php

namespace App\Scraping\Drivers\Subito;

use App\Scraping\Browser\Browser;
use App\Scraping\Contracts\ScraperDriver;
use App\Scraping\Drivers\Subito\Actions\ScrapePage;
use App\Scraping\Drivers\Subito\DTO\Item;
use App\Scraping\DTO\ScrapedItemData;
use App\Scraping\DTO\ScrapeRequestData;
use App\Scraping\Support\BlueprintInterpreter;
use HeadlessChromium\Page;

class SubitoScraperDriver implements ScraperDriver
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
        return $this->browser->wrapInPage(function (Page $page) use ($request) {
            return ScrapePage::run($page, $request->url)
                ->map(fn (Item $item) => new ScrapedItemData(
                    url: $item->link,
                    title: $item->title,
                    externalId: $item->item_id,
                ))
                ->all();
        });
    }
}
