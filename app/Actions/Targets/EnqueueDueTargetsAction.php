<?php

namespace App\Actions\Targets;

use App\Models\Target;
use Cron\CronExpression;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class EnqueueDueTargetsAction
{
    use AsAction;

    public string $commandSignature = 'enqueue:due-targets';

    public string $commandDescription = 'Dispatch scraping jobs for due targets.';

    public function handle(): int
    {
        $now = now('UTC');

        $dueTargets = Target::query()
            ->where('active', true)
            ->where(function ($q) use ($now): void {
                $q->whereNull('next_run_at')
                    ->orWhere('next_run_at', '<=', $now);
            })
            ->get();

        foreach ($dueTargets as $target) {
            RunTargetScrapeAction::dispatch($target->id);

            // Compute and store the next run based on the schedule.
            $expr = new CronExpression($target->schedule_cron);
            $next = Carbon::instance($expr->getNextRunDate($now))->setTimezone('UTC');
            $target->next_run_at = $next;
            $target->save();
        }

        return $dueTargets->count();
    }

    public function asCommand(Command $command): int
    {
        return $this->handle();
    }
}
