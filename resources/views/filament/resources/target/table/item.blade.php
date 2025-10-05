@php
    use App\Models\Target;
    use Filament\Tables\Columns\Column;
    use Filament\Tables\Columns\Layout\Component;
    /** @var Target $record */
    $record = $getRecord();

    $components = array_map(
        fn(Column|Component $component) => $component
            ->record($getRecord())
            ->recordKey($getRecordKey())
            ->rowLoop($getRowLoop())
            ->renderInLayout(),
        $getComponents(),
    );
    $getComponent = fn($name) => $components[$name] ?? null;
@endphp

<div class="px-2 py-1">
    <div class="flex items-center gap-2">
        <p class="text-xl font-medium">{{ $record->label }}</p>
        <div class="flex-1">
            {{ $getComponent('active') }}
        </div>
    </div>
    <div class="mt-2 space-y-1">
        <p><span class="text-gray-500">Articoli scansionati: </span>{{ $record->items()->count() }}</p>
        <p><span class="text-gray-500">Ultimo avvio:
            </span>{{ $record->last_run_at ? $record->last_run_at->diffForHumans() : ' - ' }}</p>
        <p><span class="text-gray-500">Prossima programmazione:
            </span>{{ $record->next_run_at ? $record->next_run_at->diffForHumans() : ' - ' }}</p>
    </div>
</div>
