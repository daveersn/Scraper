<?php

namespace App\Actions\Targets;

use App\Models\Target;
use App\Scraping\DTOs\ScrapedItemData;
use App\Scraping\DTOs\ScrapeRequestData;
use App\Scraping\ScraperRegistry;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class PreviewScrapeAction
{
    use AsAction;

    public string $commandSignature = 'scrape:preview {targetId}';

    public string $commandDescription = 'Preview extracted items for a target (no persistence).';

    public function __construct(public ScraperRegistry $registry) {}

    /**
     * @return array<int, ScrapedItemData>
     */
    public function handle(Target $target): array
    {
        $request = new ScrapeRequestData(
            targetId: $target->id,
            url: $target->url,
            blueprint: $target->blueprint ?? [],
        );

        $driver = $this->registry->resolveFor($request);

        if ($driver === null) {
            return [];
        }

        return $driver->fetchItems($request);
    }

    public function asCommand(Command $command): int
    {
        $targetId = (int) $command->argument('targetId');

        $items = $this->handle(Target::findOrFail($targetId));

        foreach ($items as $i => $dto) {
            $command->line(sprintf('%d) %s | %s | %s %s', $i + 1, $dto->url, $dto->title ?? '-', (string) ($dto->currency ?? ''), (string) ($dto->price ?? '')));
        }

        $command->info('Total: '.count($items));

        return 0;
    }
}
