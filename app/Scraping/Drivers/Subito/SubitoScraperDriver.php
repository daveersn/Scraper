<?php

namespace App\Scraping\Drivers\Subito;

use App\Scraping\Contracts\ScraperDriver;
use App\Scraping\DTO\ScrapedItemData;
use App\Scraping\DTO\ScrapeRequestData;
use App\Scraping\Support\BlueprintInterpreter;
use Illuminate\Support\Facades\Log;

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
        Log::info('chrome.start', ['target' => $request->targetId]);

        // Scaffold only: do not actually launch Chrome here.
        // In a full implementation, you would:
        // - Launch BrowserFactory with binary/flags
        // - Create page, set UA/viewport, navigate to URL
        // - Optionally wait for selector or network idle
        // - Grab HTML, pass to interpreter
        // - Follow pagination until cap is reached with conservative throttling

        $currentUrl = $request->url;
        $page = 1;
        while ($page <= max(1, $maxPages)) {
            Log::info('chrome.navigate', ['url' => $currentUrl, 'page' => $page]);

            // Placeholder: in real impl, use page->getHtml()
            $html = '';

            $extracted = $this->interpreter->extractItems($html, $request, $currentUrl);
            $items = array_merge($items, $extracted);

            // Pagination logic scaffold – look up next URL/selector in blueprint.
            $hasNext = false; // Determine via interpreter or blueprint rule.
            if (! $hasNext) {
                break;
            }

            $page++;
            usleep($throttleMs * 1000);
        }

        Log::info('chrome.stop', ['items' => count($items)]);

        return $items;
    }
}
