<?php

namespace App\Filament\Resources\Targets\Pages;

use App\Filament\Imports\ItemImporter;
use App\Filament\Imports\ItemPriceImporter;
use App\Filament\Resources\Targets\Resources\Items\ItemResource;
use App\Filament\Resources\Targets\TargetResource;
use App\Models\Target;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ManageTargetItems extends ManageRelatedRecords
{
    protected static string $resource = TargetResource::class;

    protected static string $relationship = 'items';

    protected static ?string $relatedResource = ItemResource::class;

    public function table(Table $table): Table
    {
        /** @var Target $target */
        $target = $this->getRecord();

        $driver = $target->getScraperDriver();
        $importerClass = $driver::getImporterClass();

        return $table
            ->headerActions([
                ImportAction::make('import_items')
                    ->label('Importa items')
                    ->color('primary')
                    ->icon(Heroicon::ArrowDownTray)
                    ->importer($importerClass ?? ItemImporter::class)
                    ->options([
                        'target' => $target,
                    ]),

                ImportAction::make('import_prices')
                    ->label('Importa prezzi')
                    ->color('primary')
                    ->icon(Heroicon::CurrencyEuro)
                    ->importer(ItemPriceImporter::class),
            ]);
    }
}
