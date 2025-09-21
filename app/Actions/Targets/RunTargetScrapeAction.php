<?php

namespace App\Actions\Targets;

use App\Models\Target;
use App\Scraping\DTO\ScrapeRequestData;
use App\Scraping\ScraperRegistry;
use Lorisleiva\Actions\Concerns\AsAction;

class RunTargetScrapeAction
{
    use AsAction;

    public function __construct(public ScraperRegistry $registry) {}

    public function handle(int $targetId): void
    {
        $target = Target::query()->findOrFail($targetId);

        $request = new ScrapeRequestData(
            targetId: $target->id,
            url: $target->url,
            blueprint: $target->blueprint ?? [],
            context: [],
        );

        $driver = $this->registry->resolveFor($request);
        $items = $driver ? $driver->fetchItems($request) : [];

        // Delegate persistence & scheduling updates to existing action.
        (new RunScrapeAction)->handle($target->id, $items);
    }
}
