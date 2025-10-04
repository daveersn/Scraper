<?php

namespace App\Filament\Resources\Targets\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TargetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->required(),
                TextInput::make('url')
                    ->required(),
                TextInput::make('schedule_cron'),
                Toggle::make('active')
                    ->default(true)
                    ->inline(false)
                    ->required(),
            ]);
    }
}
