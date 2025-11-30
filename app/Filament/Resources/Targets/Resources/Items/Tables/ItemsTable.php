<?php

namespace App\Filament\Resources\Targets\Resources\Items\Tables;

use App\Models\Item;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
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
                    ->visible(fn (Item $record) => $record->isActive())
                    ->openUrlInNewTab(),
                ActionGroup::make([
                    Action::make('toggle_ignore')
                        ->label(fn (Item $record) => $record->pivot->ignored ? 'Includi' : 'Ignora')
                        ->color(fn (Item $record) => ! $record->pivot->ignored ? 'danger' : 'success')
                        ->icon(fn (Item $record) => $record->pivot->ignored ? Heroicon::OutlinedCheckCircle : Heroicon::OutlinedXCircle)
                        ->requiresConfirmation()
                        ->action(fn (Item $record) => $record->pivot->update([
                            'ignored' => ! $record->pivot->ignored,
                        ])),
                ]),
            ]);
    }
}
