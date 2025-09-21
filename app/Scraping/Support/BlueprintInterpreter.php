<?php

namespace App\Scraping\Support;

use App\Scraping\DTO\ScrapedItemData;
use App\Scraping\DTO\ScrapeRequestData;

/**
 * Minimal blueprint interpreter skeleton.
 *
 * Blueprint shape (JSON) example:
 * {
 *   "item_selector": ".product",
 *   "fields": {
 *     "url": {"selector": ".title a", "attr": "href"},
 *     "title": {"selector": ".title", "text": true},
 *     "price": {"selector": ".price", "text": true, "regex": "[0-9.,]+"},
 *     "currency": {"static": "USD"},
 *     "externalId": {"selector": ".sku", "text": true}
 *   },
 *   "pagination": {"next_selector": ".next a", "max_pages": 5},
 *   "wait_selector": "#ready",
 *   "navigation_delay_ms": 250
 * }
 *
 * Normalization rules:
 * - Price: strip thousands, detect decimal, return float.
 * - Currency: honor explicit field/static, else infer symbol/code.
 */
final class BlueprintInterpreter
{
    /**
     * Parse the HTML and return ScrapedItemData per item.
     * NOTE: This is a scaffold – implement CSS selection using a DOM library.
     *
     * @return array<int, ScrapedItemData>
     */
    public function extractItems(string $html, ScrapeRequestData $request, string $baseUrl): array
    {
        // Intentionally return an empty list in scaffold.
        // Implement using Symfony DomCrawler or similar to honor the blueprint.
        return [];
    }
}
