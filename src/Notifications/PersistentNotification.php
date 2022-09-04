<?php

namespace ProtoneMedia\LaravelDuskFakes\Notifications;

class PersistentNotification
{
    public function __construct(
        public $notifiableClass,
        public $notifiableKey,
        public $notificationClass,
        public $notification
    ) {
    }
}
