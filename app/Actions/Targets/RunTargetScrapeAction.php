<?php

namespace App\Actions\Targets;

use App\Models\Target;
use App\Scraping\DTO\ScrapeRequestData;
use App\Scraping\ScraperRegistry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Lorisleiva\Actions\Concerns\AsAction;

class RunTargetScrapeAction implements ShouldQueue
{
    use AsAction;
    use InteractsWithQueue, Queueable, SerializesModels;

    public string $queue = 'default';

    public function __construct(public ScraperRegistry $registry) {}

    public function handle(int $targetId): void
    {
        $target = Target::query()->findOrFail($targetId);

        $request = new ScrapeRequestData(
            targetId: $target->id,
            url: $target->url,
            blueprint: $target->blueprint ?? [],
            currencyHint: null,
            context: [],
        );

        $driver = $this->registry->resolveFor($request);
        $items = $driver ? $driver->fetchItems($request) : [];

        // Delegate persistence & scheduling updates to existing action.
        (new RunScrapeAction)->handle($target->id, $items);
    }
}
