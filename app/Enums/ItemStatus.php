<?php

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum ItemStatus: string implements HasColor, HasLabel
{
    case ACTIVE = 'A';
    case GONE = 'G';

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTIVE => Color::Green,
            self::GONE => Color::Red,
        };
    }

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::ACTIVE => 'Attivo',
            self::GONE => 'Cancellato',
        };
    }
}
