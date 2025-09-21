<?php

namespace App\Scraping\DTOs;

use Spatie\LaravelData\Data;

class ScrapedItemData extends Data
{
    public function __construct(
        public string $url,
        public ?string $externalId = null,
        public ?string $title = null,
        public ?float $price = null,
        public ?string $currency = null,
    ) {}
}
