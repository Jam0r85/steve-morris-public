<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

$to = config('services.street.failed_jobs_email');
$env = app()->environment();
$host = gethostname();

/*
|--------------------------------------------------------------------------
| Street: Properties (SALES)
|--------------------------------------------------------------------------
*/
Schedule::command('street:sync:properties --sales')
    ->everyThirtyMinutes()
    ->withoutOverlapping(45)
    ->onOneServer()
    ->runInBackground()
    ->evenInMaintenanceMode()
    ->appendOutputTo(storage_path('logs/street-sales-sync.log'))
    ->onFailure(function (Event $event) use ($to, $env, $host): void {
        $log = storage_path('logs/street-sales-sync.log');
        $msg = "Street SALES **SCHEDULE** failed\n\n"
             . "Command: {$event->command}\n"
             . "Env: {$env}\n"
             . "Host: {$host}\n"
             . 'When: ' . now()->toDateTimeString() . "\n"
             . "Log: {$log}\n";

        Mail::raw($msg, function ($m) use ($to, $env): void {
            $m->to($to)->subject("❌ [SCHEDULE][SALES][{$env}] street:sync:properties --sales failed");
        });
    });

/*
|--------------------------------------------------------------------------
| Street: Properties (LETTINGS)
|--------------------------------------------------------------------------
*/
Schedule::command('street:sync:properties --lettings')
    ->cron('15,45 * * * *') // :15 and :45
    ->withoutOverlapping(45)
    ->onOneServer()
    ->runInBackground()
    ->evenInMaintenanceMode()
    ->appendOutputTo(storage_path('logs/street-lettings-sync.log'))
    ->onFailure(function (Event $event) use ($to, $env, $host): void {
        $log = storage_path('logs/street-lettings-sync.log');
        $msg = "Street LETTINGS **SCHEDULE** failed\n\n"
             . "Command: {$event->command}\n"
             . "Env: {$env}\n"
             . "Host: {$host}\n"
             . 'When: ' . now()->toDateTimeString() . "\n"
             . "Log: {$log}\n";

        Mail::raw($msg, function ($m) use ($to, $env): void {
            $m->to($to)->subject("❌ [SCHEDULE][LETTINGS][{$env}] street:sync:properties --lettings failed");
        });
    });

/*
|--------------------------------------------------------------------------
| Street: Branches (Open API)
|--------------------------------------------------------------------------
| If you already have a branches command with signature `street:sync:branches`,
| here’s a sensible daily schedule (adjust as you like).
*/
Schedule::command('street:sync:branches')
    ->dailyAt('02:10')
    ->withoutOverlapping(30)
    ->onOneServer()
    ->runInBackground()
    ->evenInMaintenanceMode()
    ->appendOutputTo(storage_path('logs/street-branches-sync.log'))
    ->onFailure(function (Event $event) use ($to, $env, $host): void {
        $log = storage_path('logs/street-branches-sync.log');
        $msg = "Street BRANCHES **SCHEDULE** failed\n\n"
             . "Command: {$event->command}\n"
             . "Env: {$env}\n"
             . "Host: {$host}\n"
             . 'When: ' . now()->toDateTimeString() . "\n"
             . "Log: {$log}\n";

        Mail::raw($msg, function ($m) use ($to, $env): void {
            $m->to($to)->subject("❌ [SCHEDULE][BRANCHES][{$env}] street:sync:branches failed");
        });
    });
