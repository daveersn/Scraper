<?php

namespace App\Filament\Imports;

use App\Filament\Imports\Columns\ExtraFieldImportColumn;
use App\Models\Item;
use App\Models\Target;
use App\Support\UrlNormalizer;
use Cknow\Money\Money;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Arr;
use Illuminate\Support\Number;

class ItemImporter extends Importer
{
    protected static ?string $model = Item::class;

    protected array $originalCastedData;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('url')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('external_id')
                ->rules(['max:255']),
            ImportColumn::make('title')
                ->requiredMapping()
                ->rules(['max:255']),
            ImportColumn::make('current_price')
                ->castStateUsing(fn (string $state) => Money::parse($state))
                ->requiredMapping(),
            ImportColumn::make('prices')
                ->castStateUsing(fn (?array $state) => $state !== null
                    ? array_map(fn (string $value) => Money::parse($value), $state)
                    : $state
                )
                ->multiple(),
            ImportColumn::make('status')
                ->fillRecordUsing(fn (Item $record, string $state) => $record->status = $state)
                ->guess(['status'])
                ->requiredMapping(),
            ImportColumn::make('first_seen_at')
                ->requiredMapping()
                ->rules(['date']),
            ImportColumn::make('last_seen_at')
                ->requiredMapping()
                ->rules(['date']),

            ...static::getExtraFieldsColumns(),
        ];
    }

    public static function getExtraFieldsColumns(): array
    {
        return [];
    }

    public function resolveRecord(): Item
    {
        return Item::firstOrNew([
            'url_hash' => UrlNormalizer::hash($this->data['url']),
        ]);
    }

    protected function beforeFill(): void
    {
        $options = $this->getOptions();

        /** @var Target $target */
        $target = $options['target'];
        $driver = $target->getScraperDriver();

        $this->originalCastedData = $this->data;

        $this->data = Arr::only($this->data, [
            'url',
            'external_id',
            'title',
            'status',
            'first_seen_at',
            'last_seen_at',
            'current_price',
        ]);

        if ($driver::getExtraFieldsClass() !== null && count(static::getExtraFieldsColumns())) {
            $extraFields = Arr::mapWithKeys(
                static::getExtraFieldsColumns(),
                fn (ExtraFieldImportColumn $column) => [
                    $column->getExtraFieldsName() => $this->originalCastedData[$column->getName()] ?? null,
                ]);

            $this->data['extra_fields'] = $driver::getExtraFieldsClass()::from($extraFields);
        }
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
