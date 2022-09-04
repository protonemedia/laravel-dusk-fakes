<?php

namespace ProtoneMedia\LaravelDuskFakes\Notifications;

use Illuminate\Support\Facades\Notification;

trait PersistentNotifications
{
    public function setUpPersistentNotifications()
    {
        Notification::swap(
            app(PersistentNotificationFake::class)->loadNotifications()
        );
    }

    public function tearDownPersistentNotifications()
    {
        Notification::cleanStorage();
    }
}
