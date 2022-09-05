<?php

namespace ProtoneMedia\LaravelDuskFakes\Notifications;

use Illuminate\Support\Facades\Notification;

trait PersistentNotifications
{
    public function setUpPersistentNotifications()
    {
        Notification::swap(
            app(UncachedPersistentNotificationFake::class)
        );
    }

    public function tearDownPersistentNotifications()
    {
        Notification::cleanStorage();
    }
}
