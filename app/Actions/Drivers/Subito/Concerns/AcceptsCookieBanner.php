<?php

namespace App\Actions\Drivers\Subito\Concerns;

use HeadlessChromium\Page;

trait AcceptsCookieBanner
{
    protected function acceptCookieBanner(Page $page): void
    {
        $page->evaluate("document.querySelector('.didomi-continue-without-agreeing')?.click()");
    }
}
