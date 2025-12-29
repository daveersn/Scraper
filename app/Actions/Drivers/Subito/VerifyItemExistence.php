<?php

namespace App\Actions\Drivers\Subito;

use App\Actions\Drivers\Subito\Concerns\AcceptsCookieBanner;
use App\DTO\ScrapeRequestData;
use App\Http\Integrations\Browser\Browser;
use App\Models\Item;
use HeadlessChromium\Page;
use Illuminate\Console\Command;
use Lorisleiva\Actions\Concerns\AsAction;

class VerifyItemExistence
{
    public $commandSignature = 'subito:item-exists {itemId}';

    public $commandDescription = 'Checks if a Subito Item still exists.';

    use AcceptsCookieBanner, AsAction;

    public function handle(ScrapeRequestData $request)
    {
        $browser = app(Browser::class);

        return $browser->wrapInPage(function (Page $page) use ($request) {
            $page->navigate($request->url)->waitForNavigation();

            $this->acceptCookieBanner($page);

            return $page->evaluate("document.querySelector('[class*=-info__id]') !== null")->getReturnValue();
        });
    }

    public function asCommand(Command $command): int
    {
        $item = Item::findOrFail($command->argument('itemId'));
        $exists = $this->handle(new ScrapeRequestData($item->url));

        $command->info($exists === true ? 'Exists' : 'Does not exist');

        return Command::SUCCESS;
    }
}
