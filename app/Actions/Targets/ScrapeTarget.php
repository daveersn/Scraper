<?php

namespace App\Actions\Targets;

use App\DTO\ScrapedItemData;
use App\DTO\ScrapeRequestData;
use App\Enums\ItemStatus;
use App\Models\Item;
use App\Models\Scopes\ActiveScope;
use App\Models\Target;
use App\Scraping\ScraperRegistry;
use App\Support\UrlNormalizer;
use Cron\CronExpression;
use DateTimeInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;

class ScrapeTarget
{
    use AsAction;

    public $commandSignature = 'target:scrape {targetId}';

    public $commandDescription = 'Perform Target scraping';

    public function __construct(
        public ScraperRegistry $registry,
        public ?DateTimeInterface $now = null,
    ) {}

    public function handle(Target $target): Target
    {
        $request = ScrapeRequestData::from($target);

        $driver = $this->registry->resolveFor($request);
        $items = $driver ? $driver->fetchItems($request) : [];

        $this->now = now();

        DB::transaction(function () use ($items, $target) {
            $this->updateItems($target, $items);
            $this->updateTarget($target);
        });

        return $target;
    }

    public function asCommand(Command $command): int
    {
        $target = Target::withoutGlobalScope(ActiveScope::class)->findOrFail($command->argument('targetId'));

        $this->handle($target);

        return 0;
    }

    private function updateItems(Target $target, array $items): void
    {
        foreach ($items as $item) {
            $item = $item instanceof ScrapedItemData ? $item : ScrapedItemData::from($item);

            $normalized = UrlNormalizer::normalize($item->url);
            $hash = UrlNormalizer::hash($normalized);

            $itemModel = Item::firstWhere('url_hash', $hash);

            if (! $itemModel) {
                $itemModel = Item::create([
                    'url' => $normalized,
                    'external_id' => $item->externalId,
                    'title' => $item->title,
                    'current_price' => $item->price,
                    'currency' => $item->currency,
                    'status' => ItemStatus::ACTIVE,
                    'extra_fields' => $item->extraFields,
                    'first_seen_at' => $this->now,
                    'last_seen_at' => $this->now,
                ]);

                $itemModel->prices()->create([
                    'price' => $item->price,
                    'currency' => $item->currency,
                ]);
            }

            if (! $itemModel->wasRecentlyCreated) {
                $itemModel->fill([
                    'title' => $item->title,
                    'current_price' => $item->price,
                    'last_seen_at' => $this->now,
                ]);

                // If price was changed, fill and create a new price
                if ($item->price !== $itemModel->current_price) {
                    $itemModel->current_price = $item->price;

                    $itemModel->prices()->create([
                        'price' => $item->price,
                        'currency' => $item->currency,
                    ]);
                }

                $itemModel->save();
            }

            // Sync pivot
            if (! $target->items()->where('items.id', $itemModel->getKey())->exists()) {
                $target->items()->attach($itemModel->getKey(), [
                    'first_seen_at' => $this->now,
                    'last_seen_at' => $this->now,
                ]);
            } else {
                $target->items()->updateExistingPivot($itemModel->getKey(), [
                    'last_seen_at' => $this->now,
                ]);
            }
        }
    }

    private function updateTarget(Target $target): void
    {
        $target->last_run_at = $this->now;

        if ($target->schedule_cron) {
            $expr = new CronExpression($target->schedule_cron);
            $next = Carbon::instance($expr->getNextRunDate($this->now));
            $target->next_run_at = $next;
        }

        $target->save();
    }
}
