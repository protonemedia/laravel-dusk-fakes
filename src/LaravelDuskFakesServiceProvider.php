<?php

namespace ProtoneMedia\LaravelDuskFakes;

use Illuminate\Contracts\Bus\QueueingDispatcher;
use Illuminate\Contracts\Queue\Queue as QueueContract;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use ProtoneMedia\LaravelDuskFakes\Bus\PersistentBusFake;
use ProtoneMedia\LaravelDuskFakes\Mails\PersistentMailFake;
use ProtoneMedia\LaravelDuskFakes\Notifications\PersistentNotificationFake;
use ProtoneMedia\LaravelDuskFakes\Queue\PersistentQueueFake;

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

        $this->bootFakeBus();
        $this->bootFakeMails();
        $this->bootFakeNotifications();
        $this->bootFakeQueue();
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

    private function bootFakeQueue()
    {
        if (! config('dusk-fakes.queue.enabled')) {
            return;
        }

        $fake = new PersistentQueueFake(app(), [], app(QueueContract::class));

        $this->app->singleton(
            PersistentQueueFake::class,
            fn () => $fake
        );

        Queue::swap($fake);
    }
}
