<?php

namespace App\Actions\Targets;

use App\DTO\ScrapedItemData;
use App\Models\Item;
use App\Models\ItemPrice;
use App\Models\Target;
use App\Support\UrlNormalizer;
use Cron\CronExpression;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class RunScrapeAction
{
    use AsAction;

    /**
     * @param  array<int, ScrapedItemData|array>  $scrapedItems
     */
    public function handle(int $targetId, array $scrapedItems = []): void
    {
        $target = Target::query()->findOrFail($targetId);
        $now = now('UTC');

        foreach ($scrapedItems as $item) {
            $data = $item instanceof ScrapedItemData ? $item : ScrapedItemData::from($item);

            $normalized = UrlNormalizer::normalize($data->url);
            $hash = UrlNormalizer::hash($normalized);

            $host = parse_url($normalized, PHP_URL_HOST) ?: '';

            $itemModel = null;
            if ($data->externalId !== null && $data->externalId !== '') {
                $itemModel = Item::query()
                    ->where('site_domain', strtolower((string) $host))
                    ->where('external_id', $data->externalId)
                    ->first();
            }

            if (! $itemModel) {
                $itemModel = Item::query()->where('url_hash', $hash)->first();
            }

            if (! $itemModel) {
                $itemModel = new Item;
                $itemModel->site_domain = strtolower((string) $host);
                $itemModel->url = $normalized;
                $itemModel->url_hash = $hash;
                $itemModel->external_id = $data->externalId;
                $itemModel->title = $data->title;
                $itemModel->first_seen_at = $now;
                $itemModel->last_seen_at = $now;
                if ($data->price !== null) {
                    $itemModel->current_price = $data->price;
                }
                if ($data->currency !== null) {
                    $itemModel->currency = $data->currency;
                }
                $itemModel->save();
            } else {
                // Update existing
                if ($data->title !== null) {
                    $itemModel->title = $data->title;
                }
                if ($data->price !== null) {
                    $itemModel->current_price = $data->price;
                }
                if ($data->currency !== null) {
                    $itemModel->currency = $data->currency;
                }
                $itemModel->last_seen_at = $now;
                $itemModel->save();
            }

            if ($data->price !== null) {
                ItemPrice::query()->create([
                    'item_id' => $itemModel->id,
                    'price' => $data->price,
                    'currency' => $data->currency ?? $itemModel->currency ?? 'USD',
                ]);
            }

            // Sync pivot
            $exists = $target->items()->where('items.id', $itemModel->id)->exists();
            if (! $exists) {
                $target->items()->attach($itemModel->id, [
                    'first_seen_at' => $now,
                    'last_seen_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $target->items()->updateExistingPivot($itemModel->id, [
                    'last_seen_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        // Update target timestamps & schedule
        $target->last_run_at = $now;
        $expr = CronExpression::factory($target->schedule_cron);
        $next = Carbon::instance($expr->getNextRunDate($now))->setTimezone('UTC');
        $target->next_run_at = $next;
        $target->save();
    }
}
