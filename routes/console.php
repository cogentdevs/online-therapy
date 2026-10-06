<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('content:expire-free')->daily()->withoutOverlapping();
Schedule::command('ads:expire')->daily()->withoutOverlapping();
Schedule::command('subscriptions:send-expiry-reminders')->dailyAt('00:05')->withoutOverlapping();
Schedule::command('subscriptions:expire')->dailyAt('00:10')->withoutOverlapping();
