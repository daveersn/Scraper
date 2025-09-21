<?php

namespace Tests\Unit;

use Cron\CronExpression;
use Illuminate\Support\Carbon;

test('next run date every 15 minutes', function () {
    $expr = new CronExpression('*/15 * * * *');
    $fixed = Carbon::parse('2025-01-01 12:00:00');

    $next = $expr->getNextRunDate($fixed);

    expect($next->format('Y-m-d H:i:s'))->toEqual('2025-01-01 12:15:00');
});
