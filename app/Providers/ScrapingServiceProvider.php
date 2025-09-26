<?php

namespace App\Providers;

use App\Scraping\Drivers\ChromeScraperDriver;
use App\Scraping\Drivers\ScraperDriver;
use App\Scraping\Drivers\SubitoScraperDriver;
use App\Scraping\ScraperRegistry;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class ScrapingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->tag([
            SubitoScraperDriver::class,
            ChromeScraperDriver::class,
        ], 'scraper.drivers');

        // Bind the registry to inject all tagged drivers.
        $this->app->singleton(ScraperRegistry::class, function (Application $app): ScraperRegistry {
            /** @var iterable<int, ScraperDriver> $drivers */
            $drivers = $app->tagged('scraper.drivers');

            return new ScraperRegistry($drivers);
        });
    }
}
