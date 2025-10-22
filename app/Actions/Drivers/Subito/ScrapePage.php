<?php

namespace App\Actions\Drivers\Subito;

use HeadlessChromium\Page;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsObject;

class ScrapePage
{
    use AsObject;

    public function handle(Page $page, string $url): Collection
    {
        $allItems = collect();
        $currentPageIndex = 1;

        $maxPages = config('scraping.chrome.pagination_max_pages');

        do {
            try {
                // Navigate to the current page
                $page->navigate("$url&o=$currentPageIndex")->waitForNavigation();

                // Accept Cookie Banner if present
                $page->evaluate("document.querySelector('.didomi-continue-without-agreeing')?.click()");

                // Scroll incrementally and extract items at each scroll position
                $pageItems = $this->scrollAndExtractItems($page);

                // Skip to next page if no items found
                if ($pageItems->isEmpty()) {
                    break;
                }

                // Add items to the collection
                $allItems = $allItems->merge($pageItems);

                // Check if there's a next page
                $hasNextPage = $this->hasNextPage($page);

                // Increment page index if there's a next page
                if ($hasNextPage) {
                    $currentPageIndex++;
                }
            } catch (\Exception $e) {
                report("Error scraping Subito.it page $currentPageIndex: {$e->getMessage()}");
                break;
            }
        } while ($this->hasNextPage($page) && $currentPageIndex < $maxPages);

        return $allItems;
    }

    /**
     * Scroll the page incrementally and extract items at each scroll position
     */
    protected function scrollAndExtractItems(Page $page): Collection
    {
        $allItems = collect();
        $processedIds = collect();

        try {
            /** @var int $pageHeight */
            $pageHeight = $page->evaluate('document.body.scrollHeight')->getReturnValue();
            $innerHeight = $page->evaluate('window.innerHeight')->getReturnValue();

            // Scroll page incrementally with overlap to ensure we don't miss any items
            // Use 75% of the viewport height as the scroll increment for better overlap
            $scrollIncrement = (int) ($innerHeight * 0.75);
            $totalScrolls = ceil($pageHeight / $scrollIncrement);

            for ($i = 0; $i < $totalScrolls; $i++) {
                // Scroll to the current position
                $currentHeight = $scrollIncrement * $i;
                $page->evaluate("window.scrollTo(0, $currentHeight)");

                // Wait for content to load
                usleep(0.1 * 1000000); // 100ms delay

                // Extract items visible in the current viewport
                $rawItems = ExtractItemsFromPage::run($page);

                if ($rawItems->isNotEmpty()) {
                    // Normalize items to DTOs
                    $items = NormalizeItem::run($rawItems);

                    // Filter out already processed items to avoid duplicates
                    $newItems = $items->filter(function ($item) use ($processedIds) {
                        if ($processedIds->contains($item->item_id)) {
                            return false;
                        }

                        $processedIds->push($item->item_id);

                        return true;
                    });

                    // Add new items to the collection
                    $allItems = $allItems->merge($newItems);
                }
            }
        } catch (\Exception $e) {
            report("Error scrolling page and extracting items: {$e->getMessage()}");
        }

        return $allItems;
    }

    protected function hasNextPage(Page $page): bool
    {
        try {
            return ! ($page->evaluate("document.querySelector('.pagination-container > button:last-child')?.disabled")->getReturnValue() ?? true);
        } catch (\Exception $e) {
            report("Error checking for next page: {$e->getMessage()}");

            return false;
        }
    }
}
