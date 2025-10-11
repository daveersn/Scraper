<?php

namespace App\Observers;

use App\DTO\ScrapeRequestData;
use App\Models\Target;
use App\Scraping\ScraperRegistry;

class TargetObserver
{
    public function __construct(public ScraperRegistry $registry) {}

    public function creating(Target $target): void
    {
        $driver = $this->registry->resolveFor(ScrapeRequestData::from($target));

        $target->driver = $driver::getDriverType();
    }
}
