<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class ExtraFieldsCastWrapper extends Data
{
    public function __construct(
        public string $className,
        public array $data
    ) {}

    public function getExtraFieldsDTO(): ExtraFields
    {
        return $this->className::from($this->data);
    }
}
