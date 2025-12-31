<?php

use App\Actions\Drivers\Subito\VerifyItemExistence;
use App\Actions\Targets\EnqueueDueTargets;
use App\Actions\Targets\PreviewScrape;
use App\Actions\Targets\ScrapeTarget;
use App\Actions\Targets\UpdateGoneItems;
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
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withCommands([
        PreviewScrape::class,
        ScrapeTarget::class,
        EnqueueDueTargets::class,
        UpdateGoneItems::class,
        VerifyItemExistence::class,
    ])
    ->withSchedule(function (\Illuminate\Console\Scheduling\Schedule $schedule) {
        $schedule->command(ChromeKillCommand::class)->dailyAt('03:00');
        $schedule->command(EnqueueDueTargets::class)->dailyAt('02:00');
        $schedule->command(UpdateGoneItems::class)->dailyAt('04:00');
    })
    ->create();
