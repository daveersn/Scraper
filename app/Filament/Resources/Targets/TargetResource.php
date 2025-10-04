<?php

namespace App\Filament\Resources\Targets;

use App\Filament\Resources\Targets\Pages\ManageTargetItems;
use App\Filament\Resources\Targets\Pages\ManageTargets;
use App\Filament\Resources\Targets\Schemas\TargetForm;
use App\Filament\Resources\Targets\Tables\TargetTable;
use App\Models\Target;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TargetResource extends Resource
{
    protected static ?string $model = Target::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'label';

    public static function form(Schema $schema): Schema
    {
        return TargetForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TargetTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTargets::route('/'),
            'items' => ManageTargetItems::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }
}
