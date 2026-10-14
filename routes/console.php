<?php

use App\Models\Task;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('theme:toggle dark')->dailyAt('18:16');

Schedule::command('theme:toggle dark')->everyMinute();
Schedule::command('theme:toggle light')->everyTwoMinutes();

Schedule::call(function () {
    Task::onlyTrashed()
        ->where('deleted_at', '<=', now()->subDays(30))
        ->forceDelete();
})->dailyAt('17:20');
