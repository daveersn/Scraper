<?php

namespace App\DTO;

use App\DTO\ExtraFields as Data;
use App\Enums\SubitoItemStatus;
use DateTimeInterface;

class SubitoExtraFields extends Data
{
    public function __construct(
        public string $town,
        public DateTimeInterface $uploadedDateTime,
        public ?SubitoItemStatus $status,
    ) {}
}
