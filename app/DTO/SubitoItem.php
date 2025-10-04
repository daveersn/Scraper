<?php

namespace App\DTO;

use App\Enums\SubitoItemStatus;
use Cknow\Money\Money;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class SubitoItem extends Data
{
    public function __construct(
        public int $item_id,
        public string $title,
        public Money $price,
        public string $town,
        public Carbon $uploadedDateTime,
        public ?SubitoItemStatus $status,
        public string $link
    ) {}
}
