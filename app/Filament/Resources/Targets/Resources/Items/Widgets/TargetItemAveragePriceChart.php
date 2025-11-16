<?php

namespace App\Filament\Resources\Targets\Resources\Items\Widgets;

use App\Models\ItemPrice;
use App\Models\Target;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TargetItemAveragePriceChart extends ChartWidget
{
    public ?Target $record = null;

    protected ?string $heading = 'Prezzo medio per mese';

    protected int|string|array $columnSpan = 2;

    protected static bool $isLazy = false;

    protected function getData(): array
    {
        $points = $this->getMonthlyAveragePrices();

        return [
            'datasets' => [
                [
                    'label' => 'Prezzo medio',
                    'data' => $points->pluck('value'),
                    'borderColor' => '#2563eb',
                    'backgroundColor' => 'rgba(37,99,235,0.15)',
                    'fill' => 'start',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $points->pluck('label'),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return Collection<int, array{label: string, value: float}>
     */
    private function getMonthlyAveragePrices(): Collection
    {
        $targetId = $this->record?->getKey();

        if ($targetId === null) {
            return collect();
        }

        $prices = ItemPrice::query()
            ->whereHas('item.targets', fn (Builder $query) => $query->whereKey($targetId))
            ->orderBy('created_at')
            ->get(['id', 'price', 'created_at']);

        return $prices
            ->filter(fn (ItemPrice $price) => $price->created_at !== null)
            ->groupBy(fn (ItemPrice $price) => $price->created_at->startOfMonth()->format('Y-m'))
            ->map(function (Collection $group, string $month): array {
                $average = $group->avg(fn (ItemPrice $price) => (float) $price->getRawOriginal('price'));
                $label = Carbon::createFromFormat('Y-m', $month)->translatedFormat('M Y');

                return [
                    'label' => $label,
                    'value' => round($average / 100, 2),
                ];
            })
            ->values();
    }
}
