<?php

namespace Tests\Feature;

use App\Actions\Targets\RunScrapeAction;
use App\Models\Item;
use App\Models\ItemPrice;
use App\Models\Target;
use App\Models\User;
use App\Scraping\DTOs\ScrapedItemData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class RunScrapeActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_upsert_flow_and_pivot_sync(): void
    {
        $user = User::factory()->create();
        $target = Target::query()->create([
            'user_id' => $user->id,
            'label' => 'Test Target',
            'url' => 'https://example.com/list',
            'driver' => 'http',
            'schedule_cron' => '*/60 * * * *',
            'active' => true,
        ]);

        Carbon::setTestNow(Carbon::create(2024, 1, 1, 12, 0, 0, 'UTC'));

        $sameItemA = new ScrapedItemData(
            url: 'https://shop.test/product?id=100&b=2&a=1&utm_source=x',
            title: 'Widget',
            price: 10.00,
            currency: 'EUR',
        );

        $sameItemB = new ScrapedItemData(
            url: 'https://shop.test/product?a=1&id=100&b=2#frag',
            title: 'Widget X',
            price: 9.50,
            currency: 'EUR',
        );

        $externalItem = new ScrapedItemData(
            url: 'https://shop.test/product/sku/ABC',
            externalId: 'ABC',
            title: 'SKU Item',
            price: 5.00,
            currency: 'EUR',
        );

        (new RunScrapeAction)->handle($target->id, [$sameItemA, $sameItemB, $externalItem]);

        // Expect one item for the same URL with two price rows.
        $items = Item::query()->where('site_domain', 'shop.test')->get();
        $this->assertCount(2, $items);

        $urlItems = Item::query()->where('url', 'like', 'https://shop.test/product%')->get();
        $this->assertCount(2, $urlItems);

        // Find the deduped item by URL hash (the one with id=100)
        $deduped = $urlItems->firstWhere('external_id', null);
        $this->assertNotNull($deduped);
        $this->assertSame('Widget X', $deduped->title); // updated by second record
        $this->assertEquals(9.50, (float) $deduped->current_price);

        $priceCount = ItemPrice::query()->where('item_id', $deduped->id)->count();
        $this->assertSame(2, $priceCount);

        // External ID item matches by (site_domain, external_id)
        $ext = $urlItems->firstWhere('external_id', 'ABC');
        $this->assertNotNull($ext);
        $this->assertSame('SKU Item', $ext->title);
        $this->assertEquals(5.00, (float) $ext->current_price);

        // Pivot assertions
        $this->assertDatabaseHas('target_item', [
            'target_id' => $target->id,
            'item_id' => $deduped->id,
        ]);
        $this->assertDatabaseHas('target_item', [
            'target_id' => $target->id,
            'item_id' => $ext->id,
        ]);

        $pivot = $target->items()->where('items.id', $deduped->id)->first()->pivot;
        $this->assertNotNull($pivot->first_seen_at);
        $this->assertNotNull($pivot->last_seen_at);

        // Target timestamps updated
        $target->refresh();
        $this->assertNotNull($target->last_run_at);
        $this->assertNotNull($target->next_run_at);

        Carbon::setTestNow();
    }
}
