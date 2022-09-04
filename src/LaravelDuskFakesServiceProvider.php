<?php

namespace ProtoneMedia\LaravelDuskFakes;

use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use ProtoneMedia\LaravelDuskFakes\Notifications\PersistentNotificationFake;

class LaravelDuskFakesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/dusk-fakes.php',
            'dusk-fakes'
        );
    }

    public function boot()
    {
        if (! config('dusk-fakes.notifications.enabled')) {
            return;
        }

        $fake = new PersistentNotificationFake;

        $this->app->singleton(
            PersistentNotificationFake::class,
            fn () => $fake
        );

        Notification::swap($fake);
    }
}
