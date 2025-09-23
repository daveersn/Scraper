<?php

namespace App\Scraping\DTO;

use Cknow\Money\Money;
use Spatie\LaravelData\Data;

class ScrapedItemData extends Data
{
    public function __construct(
        public string $url,
        public string $title,
        public ?string $externalId = null,
        public ?Money $price = null,
        public ?string $currency = null,
    ) {}
}
