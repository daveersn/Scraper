<?php

namespace App\DTO;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class ScrapeRequestData extends Data
{
    public function __construct(
        public string $url,
        #[MapInputName('id')]
        public ?int $targetId = null,
        public ?array $blueprint = null,
        public ?array $context = null,
    ) {}
}
