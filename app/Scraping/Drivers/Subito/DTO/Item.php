<?php

namespace App\Scraping\Drivers\Subito\DTO;

use App\Scraping\Drivers\Subito\Enums\ItemStatus;
use Cknow\Money\Money;
use DateTimeInterface;
use Spatie\LaravelData\Data;

class Item extends Data
{
    public function __construct(
        public int $item_id,
        public string $title,
        public Money $price,
        public string $town,
        public DateTimeInterface $uploadedDateTime,
        public ?ItemStatus $status,
        public string $link
    ) {}
}
