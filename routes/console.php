<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('theme:toggle dark')->dailyAt('18:16');

Schedule::command('theme:toggle dark')->everyMinute();
Schedule::command('theme:toggle light')->everyMinute();
