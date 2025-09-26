<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class ScrapeRequestData extends Data
{
    public function __construct(
        public int $targetId,
        public string $url,
        public array $blueprint = [],
        public array $context = [],
    ) {}
}
