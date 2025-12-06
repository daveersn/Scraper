<?php

namespace App\Scraping\Drivers\Contracts;

use App\DTO\ScrapeRequestData;

interface ChecksItemExistence
{
    public function itemExists(ScrapeRequestData $request): bool;
}
