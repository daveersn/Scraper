<?php

namespace App\Filament\Resources\Targets\Resources\Items\Tables;

use App\Filament\Imports\ItemImporter;
use App\Models\Item;
use Filament\Actions\Action;
use Filament\Actions\ImportAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('current_price')
                    ->label('Prezzo')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Stato')
                    ->badge()
                    ->searchable(),
                TextColumn::make('first_seen_at')
                    ->label('Scansionato il')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_seen_at')
                    ->label('Ultima scansione il')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('visit')
                    ->label('Apri pagina')
                    ->icon(Heroicon::GlobeAlt)
                    ->url(fn (Item $record) => $record->url)
                    ->openUrlInNewTab(),
            ])
            ->headerActions([
                ImportAction::make('import')
                    ->label('Importa')
                    ->color('primary')
                    ->icon(Heroicon::ArrowDownTray)
                    ->importer(ItemImporter::class),
            ]);
    }
}
