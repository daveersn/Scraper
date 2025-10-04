<?php

namespace App\Filament\Resources\Targets\Tables;

use App\Actions\Targets\PreviewScrapeAction;
use App\Filament\Resources\Targets\TargetResource;
use App\Models\Target;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

class TargetTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Split::make([
                    Stack::make([
                        Split::make([
                            TextColumn::make('label')
                                ->grow(false)
                                ->searchable(),
                            IconColumn::make('active')
                                ->boolean(),
                        ]),
                        TextColumn::make('last_run_at')
                            ->formatStateUsing(fn (?Carbon $state) => $state ? new HtmlString("<span class='text-gray-500'>Ultimo avvio: </span> <span>{$state->diffForHumans()}</span>") : null),
                        TextColumn::make('next_run_at')
                            ->formatStateUsing(fn (?Carbon $state) => $state ? new HtmlString("<span class='text-gray-500'>Prossima programmazione: </span> <span>{$state->diffForHumans()}</span>") : null),
                    ]),
                ]),
            ])
            ->filters([
                //
            ])
            ->recordUrl(fn (Target $record) => TargetResource::getUrl('items', ['record' => $record]))
            ->recordActions([
                ActionGroup::make([
                    Action::make('scrape')
                        ->icon(Heroicon::GlobeAlt)
                        ->color(Color::Green)
                        ->action(function (Target $record) {
                            PreviewScrapeAction::run($record);
                        }),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->contentGrid([
                'default' => 3,
            ]);
    }
}
