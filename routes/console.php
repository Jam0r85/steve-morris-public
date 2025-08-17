<?php

declare(strict_types=1);

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('street:sales:sync')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground();

Schedule::command('street:lettings:sync')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->onOneServer()
    ->runInBackground()
    ->cron('15,45 * * * *'); // run at :15 and :45
