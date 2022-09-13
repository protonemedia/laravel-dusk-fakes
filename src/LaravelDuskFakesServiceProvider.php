<?php

namespace ProtoneMedia\LaravelDuskFakes;

use Illuminate\Contracts\Bus\QueueingDispatcher;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use ProtoneMedia\LaravelDuskFakes\Bus\PersistentBusFake;
use ProtoneMedia\LaravelDuskFakes\Mails\PersistentMailFake;
use ProtoneMedia\LaravelDuskFakes\Notifications\PersistentNotificationFake;

class LaravelDuskFakesServiceProvider extends ServiceProvider implements DeferrableProvider
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

        $this->bootFakeBus();
        $this->bootFakeMails();
        $this->bootFakeNotifications();
    }

    private function bootFakeBus()
    {
        if (! config('dusk-fakes.bus.enabled')) {
            return;
        }

        $fake = new PersistentBusFake(app(QueueingDispatcher::class));

        $this->app->singleton(
            PersistentBusFake::class,
            fn () => $fake
        );

        Bus::swap($fake);
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
