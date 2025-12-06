<?php

namespace App\Actions\Targets;

use App\DTO\ScrapeRequestData;
use App\Http\Integrations\Browser\Browser;
use App\Models\Item;
use App\Scraping\ScraperRegistry;
use HeadlessChromium\Page;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class VerifyItemExists
{
    public $commandSignature = 'target:item-exists {itemId}';

    public $commandDescription = 'Checks if an item still exists.';

    use AsAction;

    public function __construct(
        private readonly ScraperRegistry $registry
    ) {}

    public function handle(ScrapeRequestData $request)
    {
        $browser = app(Browser::class);

        return $browser->wrapInPage(static function (Page $page) use ($request) {
            $page->navigate($request->url)->waitForNavigation();

            // Accept Cookie Banner if present
            $page->evaluate("document.querySelector('.didomi-continue-without-agreeing')?.click()");

            return $page->evaluate("document.querySelector('[class*=-info__id]') !== null")->getReturnValue();
        });
    }

    public function asCommand(Command $command): int
    {
        $item = Item::findOrFail($command->argument('itemId'));
        $exists = $this->handle(new ScrapeRequestData($item->url));

        $command->info($exists === true ? 'Valid' : 'Invalid');

        return Command::SUCCESS;
    }
}
