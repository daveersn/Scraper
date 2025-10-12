<?php

namespace App\Observers;

use App\Models\Item;
use App\Support\UrlNormalizer;

class ItemObserver
{
    public function saving(Item $item): void
    {
        if ($item->wasChanged('url') || $item->url_hash === null) {
            $item->url_hash = UrlNormalizer::hash($item->url);
        }
    }
}
