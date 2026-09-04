<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Goal;
use App\Jobs\SendDepositReminderJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $goals = Goal::where('deposit_frequency', 'daily')
        ->whereNotNull('phone_number')
        ->get();
    foreach ($goals as $goal) {
        SendDepositReminderJob::dispatch($goal);
    }
})->daily();

Schedule::call(function () {
    $goals = Goal::where('deposit_frequency', 'weekly')
        ->whereNotNull('phone_number')
        ->get();
    foreach ($goals as $goal) {
        SendDepositReminderJob::dispatch($goal);
    }
})->weekly();

Schedule::call(function () {
    $goals = Goal::where('deposit_frequency', 'monthly')
        ->whereNotNull('phone_number')
        ->get();
    foreach ($goals as $goal) {
        SendDepositReminderJob::dispatch($goal);
    }
})->monthly();
