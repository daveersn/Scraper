<?php

namespace App\Filament\Imports;

use App\Models\Item;
use App\Models\ItemPrice;
use App\Support\UrlNormalizer;
use Cknow\Money\Money;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ItemPriceImporter extends Importer
{
    protected static ?string $model = ItemPrice::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('item')
                ->requiredMapping()
                ->relationship(resolveUsing: fn (string $state) => Item::query()
                    ->where('url_hash', UrlNormalizer::hash($state))
                    ->first()
                )
                ->rules(['required']),
            ImportColumn::make('price')
                ->requiredMapping()
                ->castStateUsing(fn (string $state) => Money::parse($state))
                ->rules(['required']),
            ImportColumn::make('created_at')
                ->fillRecordUsing(fn (ItemPrice $record, string $state) => $record->fill([
                    'created_at' => $state,
                    'updated_at' => $state,
                ]))
                ->requiredMapping()
                ->rules(['required', 'date']),
        ];
    }

    public function resolveRecord(): ItemPrice
    {
        return ItemPrice::firstOrNew([
            'price' => $this->data['price'],
            'created_at' => $this->data['created_at'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your item price import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
