<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
use Illuminate\Support\Facades\Schedule;

// Minden nap hajnali 3-kor magától lefut és frissíti a naptárat
Schedule::call(function () {
    (new App\Http\Controllers\EventController)->syncFacebookEvents();
})->dailyAt('03:00');