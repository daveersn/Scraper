<?php

use App\Actions\Targets\EnqueueDueTargetsAction;
use App\Actions\Targets\PreviewScrapeAction;
use App\Actions\Targets\ScrapeTarget;
use App\Console\Commands\ChromeKillCommand;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule): void {
        $schedule->command('enqueue:due-targets')->everyMinute();
    })
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withCommands([
        PreviewScrapeAction::class,
        ScrapeTarget::class,
        EnqueueDueTargetsAction::class,
    ])
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command(ChromeKillCommand::class)->dailyAt('03:00');
    })
    ->create();
