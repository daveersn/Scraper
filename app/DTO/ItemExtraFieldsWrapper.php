<?php

namespace App\DTO;

use Spatie\LaravelData\Data;

class ItemExtraFieldsWrapper extends Data
{
    public function __construct(
        public ExtraFields $data,
    ) {}
}
