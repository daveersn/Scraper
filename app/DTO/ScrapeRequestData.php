<?php

namespace App\DTO;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class ScrapeRequestData extends Data
{
    public function __construct(
        #[MapInputName('id')]
        public int $targetId,
        public string $url,
        public ?array $blueprint = null,
        public ?array $context = null,
    ) {}
}
