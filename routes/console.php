<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

$to = config('services.street.failed_jobs_email');
$env = app()->environment();
$host = gethostname();

Schedule::command('street:sales:sync')
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
            $m->to($to)->subject("❌ [SCHEDULE][SALES][{$env}] street:sales:sync failed");
        });
    });

Schedule::command('street:lettings:sync')
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
            $m->to($to)->subject("❌ [SCHEDULE][LETTINGS][{$env}] street:lettings:sync failed");
        });
    });
