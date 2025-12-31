<?php

namespace App\Actions\Targets;

use App\Models\Target;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Lorisleiva\Actions\Concerns\AsAction;

class EnqueueDueTargets
{
    use AsAction;

    public string $commandSignature = 'target:enqueue-due-targets';

    public string $commandDescription = 'Dispatch scraping jobs for due targets.';

    public function handle(): void
    {
        Target::query()
            ->whereNotNull('schedule_cron')
            ->where(fn (Builder $query) => $query
                ->whereNull('next_run_at')
                ->orWhere('next_run_at', '<=', now()))
            ->each(fn (Target $target) => ScrapeTarget::dispatch($target));
    }

    public function asCommand(Command $command): int
    {
        $this->handle();

        return 0;
    }
}
