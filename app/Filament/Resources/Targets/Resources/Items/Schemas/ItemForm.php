<?php

namespace App\Filament\Resources\Targets\Resources\Items\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('url')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('url_hash')
                    ->required(),
                TextInput::make('external_id'),
                TextInput::make('title'),
                TextInput::make('current_price')
                    ->numeric(),
                TextInput::make('currency'),
                TextInput::make('status')
                    ->required(),
                TextInput::make('extra_fields'),
                TextInput::make('driver_type'),
                DateTimePicker::make('first_seen_at'),
                DateTimePicker::make('last_seen_at'),
            ]);
    }
}
