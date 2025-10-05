<?php

namespace App\Filament\Resources\Targets\Pages;

use App\Filament\Resources\Targets\Resources\Items\ItemResource;
use App\Filament\Resources\Targets\TargetResource;
use Filament\Resources\Pages\ManageRelatedRecords;

class ManageTargetItems extends ManageRelatedRecords
{
    protected static string $resource = TargetResource::class;

    protected static string $relationship = 'items';

    protected static ?string $relatedResource = ItemResource::class;
}
