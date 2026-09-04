<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use App\Notifications\ArkeselChannel;

class NotificationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->app->make(ChannelManager::class)->extend('arkesel', function () {
            return new ArkeselChannel();
        });
    }
}
