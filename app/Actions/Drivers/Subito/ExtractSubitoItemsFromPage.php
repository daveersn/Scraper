<?php

namespace App\Actions\Drivers\Subito;

use HeadlessChromium\Page;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsObject;

class ExtractSubitoItemsFromPage
{
    use AsObject;

    /**
     * Execute JavaScript to extract all item data from the Subito.it page
     */
    public function handle(Page $page): Collection
    {
        // JavaScript to extract item data from visible elements in the viewport
        $javascript = <<<'JS'
        [...document.querySelectorAll('.items__item.item-card')].filter(item => {
            // Check if the element is visible in the viewport
            const rect = item.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }).map(item => {
            const titleElement = item.querySelector('[class*="SmallCard-module_item-title"]');
            const priceElement = item.querySelector('[class*="SmallCard-module_price"]');
            const townElement = item.querySelector('[class*="index-module_town"]');
            const dateElement = item.querySelector('[class*="index-module_date"]');
            const statusElement = item.querySelectorAll('[class*="index-module_info"]')[0];
            const linkElement = item.querySelector('a');

            return {
                title: titleElement ? titleElement.innerText.trim() : null,
                price: priceElement ? priceElement.innerText.trim() : null,
                town: townElement ? townElement.innerText.trim() : null,
                uploaded: dateElement ? dateElement.innerText.trim() : null,
                status: statusElement ? statusElement.innerText.trim() : null,
                href: linkElement ? linkElement.href : null
            };
        }).filter(item => item.title && item.price && item.town && item.uploaded && item.href);
        JS;

        try {
            return collect($page->evaluate($javascript)->getReturnValue());
        } catch (\Exception $e) {
            report("Failed to extract items from Subito.it page: {$e->getMessage()}");

            return collect();
        }
    }
}
