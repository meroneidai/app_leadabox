<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Runs once a day; the command itself refuses to remind the same invoice twice.
Schedule::command('invoices:remind')
    ->dailyAt('08:00')
    ->timezone('Europe/Stockholm')
    ->withoutOverlapping();
