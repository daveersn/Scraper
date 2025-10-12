<?php

namespace App\Filament\Imports\Columns;

use Filament\Actions\Imports\ImportColumn;

class ExtraFieldImportColumn extends ImportColumn
{
    public const string ColumnPrefix = 'extra_field_';

    protected function setUp(): void
    {
        $this->name(self::ColumnPrefix.$this->getName());

        $this->fillRecordUsing(fn () => null);
    }

    public function getExtraFieldsName(): string
    {
        return str_replace(self::ColumnPrefix, '', $this->getName());
    }
}
