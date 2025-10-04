<?php

namespace App\Filament\Resources\Targets\Pages;

use App\Filament\Resources\Targets\Resources\Items\ItemResource;
use App\Filament\Resources\Targets\TargetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;

class ManageTargetItems extends ManageRelatedRecords
{
    protected static string $resource = TargetResource::class;

    protected static string $relationship = 'items';

    protected static ?string $relatedResource = ItemResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
