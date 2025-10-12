<?php

namespace App\Filament\Imports;

use App\Filament\Imports\Columns\ExtraFieldImportColumn;
use Illuminate\Support\Carbon;

class SubitoItemImporter extends ItemImporter
{
    public static function getExtraFieldsColumns(): array
    {
        return [
            ExtraFieldImportColumn::make('town')
                ->guess(['town']),
            ExtraFieldImportColumn::make('uploadedDateTime')
                ->castStateUsing(fn (?string $state) => $state ? Carbon::parse($state) : null)
                ->guess(['uploadedDateTime', 'uploaded_date_time'])
                ->rules(['date']),
            ExtraFieldImportColumn::make('status')
                ->label('Subito Status')
                ->rules(['max:1']),
        ];
    }
}
