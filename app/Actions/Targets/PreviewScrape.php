<?php

namespace App\Actions\Targets;

use App\Console\Commands\Concerns\PrintsPrettyJson;
use App\DTO\ScrapedItemData;
use App\DTO\ScrapeRequestData;
use App\Models\Target;
use App\Scraping\ScraperRegistry;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class PreviewScrape
{
    use AsAction, PrintsPrettyJson;

    public string $commandSignature = 'scrape:preview {targetId}';

    public string $commandDescription = 'Preview extracted items for a target (no persistence).';

    public function __construct(public ScraperRegistry $registry) {}

    /**
     * @return array<int, ScrapedItemData>
     */
    public function handle(Target $target): array
    {
        $request = new ScrapeRequestData(
            url: $target->url,
            targetId: $target->id,
            blueprint: $target->blueprint ?? [],
        );

        $driver = $target->getScraperDriver();

        if ($driver === null) {
            return [];
        }

        return $driver->fetchItems($request);
    }

    public function asCommand(Command $command): int
    {
        $targetId = (int) $command->argument('targetId');

        $items = $this->handle(Target::findOrFail($targetId));

        $this->printPrettyJson($items, $command);

        return 0;
    }
}
