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
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Table;

class TargetTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                View::make('filament.resources.target.table.item')
                    ->components([
                        'active' => IconColumn::make('active')
                            ->boolean(),
                    ]),
            ])
            ->filters([
                //
            ])
            ->selectable(false)
            ->extraAttributes([
                'class' => 'target-table',
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
                'default' => 1,
                'xl' => 2,
                '2xl' => 3,
            ]);
    }
}
