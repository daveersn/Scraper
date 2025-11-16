<?php

namespace App\Filament\Resources\Targets\Resources\Items\Widgets;

use App\Models\Target;
use Cknow\Money\Money;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TargetItemPriceStats extends StatsOverviewWidget
{
    public ?Target $record = null;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $aggregates = $this->record
            ->items()
            ->whereNotNull('current_price')
            ->selectRaw('AVG(current_price) as avg_price, MIN(current_price) as min_price, MAX(current_price) as max_price')
            ->toBase()
            ->first();

        return [
            Stat::make('Prezzo medio', Money::parse((int) $aggregates->avg_price))
                ->description('Valore medio degli articoli')
                ->icon(Heroicon::ChartBar),
            Stat::make('Prezzo minimo', Money::parse($aggregates->min_price))
                ->description('Storico completo')
                ->icon(Heroicon::ArrowTrendingDown),
            Stat::make('Prezzo massimo', Money::parse($aggregates->max_price))
                ->description('Storico completo')
                ->icon(Heroicon::ArrowTrendingUp),
        ];
    }
}
