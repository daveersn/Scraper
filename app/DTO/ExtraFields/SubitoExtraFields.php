<?php

namespace App\DTO\ExtraFields;

use App\DTO\ExtraFields\ExtraFields as Data;
use App\Enums\SubitoItemStatus;
use Illuminate\Support\Carbon;

class SubitoExtraFields extends Data
{
    public function __construct(
        public string $town,
        public Carbon $uploadedDateTime,
        public ?SubitoItemStatus $status,
    ) {}
}
