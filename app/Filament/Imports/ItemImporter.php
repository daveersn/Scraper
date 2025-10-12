<?php

namespace App\Filament\Imports;

use App\Filament\Imports\Columns\ExtraFieldImportColumn;
use App\Models\Item;
use App\Models\Target;
use App\Support\UrlNormalizer;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Arr;
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
                ->requiredMapping()
                ->rules(['max:255', 'required']),
            ImportColumn::make('status')
                ->fillRecordUsing(fn (Item $record, string $state) => $record->status = $state)
                ->guess(['status', 'required'])
                ->requiredMapping(),
            ImportColumn::make('first_seen_at')
                ->requiredMapping()
                ->rules(['date', 'required']),
            ImportColumn::make('last_seen_at')
                ->requiredMapping()
                ->rules(['date', 'required']),

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

    public function afterFill(): void
    {
        /** @var Target $target */
        $target = $this->getOptions()['target'];
        $driver = $target->getScraperDriver();

        if ($driver::getExtraFieldsClass() !== null && count(static::getExtraFieldsColumns())) {
            $extraFields = Arr::mapWithKeys(
                static::getExtraFieldsColumns(),
                fn (ExtraFieldImportColumn $column) => [
                    $column->getExtraFieldsName() => $this->data[$column->getName()] ?? null,
                ]);

            // Manually fill with hook as there is no extra_fields ImportColumn
            $this->record->fill([
                'extra_fields' => $driver::getExtraFieldsClass()::from($extraFields),
            ]);
        }
    }

    public function afterSave(): void
    {
        /** @var Target $target */
        $target = $this->getOptions()['target'];

        if (! $target->items()->where('items.id', $this->getRecord()->getKey())->exists()) {
            $target->items()->attach($this->getRecord()->getKey(), [
                'first_seen_at' => $this->data['first_seen_at'],
                'last_seen_at' => $this->data['last_seen_at'],
            ]);
        } else {
            $target->items()->updateExistingPivot($this->getRecord()->getKey(), [
                'last_seen_at' => $this->data['last_seen_at'],
            ]);
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
