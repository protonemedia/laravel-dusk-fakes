<?php

namespace ProtoneMedia\LaravelDuskFakes;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use ProtoneMedia\LaravelDuskFakes\Mails\PersistentMailFake;
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
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/dusk-fakes.php' => config_path('dusk-fakes.php'),
            ], 'config');
        }

        $this->bootFakeMails();
        $this->bootFakeNotifications();
    }

    private function bootFakeMails()
    {
        if (! config('dusk-fakes.mails.enabled')) {
            return;
        }

        $fake = new PersistentMailFake;

        $this->app->singleton(
            PersistentMailFake::class,
            fn () => $fake
        );

        Mail::swap($fake);
    }

    private function bootFakeNotifications()
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
