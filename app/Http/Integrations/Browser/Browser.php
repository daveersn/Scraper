<?php

namespace App\Http\Integrations\Browser;

class Browser extends \HeadlessChromium\Browser
{
    public function wrapInPage(callable $callback): mixed
    {
        $page = $this->createPage();

        try {
            return $callback($page);
        } finally {
            $page->close();
        }
    }
}
