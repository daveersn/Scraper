<?php

namespace App\Scraping\DTO\Drivers\Subito;

use App\Enums\Drivers\Subito\SubitoItemStatus;
use Cknow\Money\Money;
use DateTimeInterface;
use Spatie\LaravelData\Data;

class SubitoItem extends Data
{
    public function __construct(
        public int $item_id,
        public string $title,
        public Money $price,
        public string $town,
        public DateTimeInterface $uploadedDateTime,
        public ?SubitoItemStatus $status,
        public string $link
    ) {}
}
