<?php

namespace App\Filament\Imports;

use App\Models\Item;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ItemImporter extends Importer
{
    protected static ?string $model = Item::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('url')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('external_id')
                ->rules(['max:255']),
            ImportColumn::make('title')
                ->rules(['max:255']),
            ImportColumn::make('current_price')
                ->numeric()
                ->rules(['integer']),
            ImportColumn::make('currency')
                ->rules(['max:3']),
            ImportColumn::make('status')
                ->requiredMapping()
                ->rules(['required', 'max:1']),
            ImportColumn::make('extra_fields'),
            ImportColumn::make('first_seen_at')
                ->rules(['datetime']),
            ImportColumn::make('last_seen_at')
                ->rules(['datetime']),
        ];
    }

    public function resolveRecord(): Item
    {
        return Item::firstOrNew([
            'url' => $this->data['url'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your item import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
