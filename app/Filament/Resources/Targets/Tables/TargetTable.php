<?php

namespace App\Filament\Resources\Targets\Tables;

use App\Actions\Targets\ScrapeTarget;
use App\Filament\Resources\Targets\TargetResource;
use App\Models\Target;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
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
                            ScrapeTarget::dispatch($record);
                            Notification::make()
                                ->success()
                                ->title('Scansione messa in coda')
                                ->body("La tua scansione per $record->label è stata messa in coda.")
                                ->send();
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
