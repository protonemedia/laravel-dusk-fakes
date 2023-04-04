<?php

namespace ProtoneMedia\LaravelDuskFakes\Notifications;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Testing\Fakes\NotificationFake;

class PersistentNotificationFake extends NotificationFake
{
    private string $directory;

    private string $storage;

    public function __construct()
    {
        $this->directory = rtrim(config('dusk-fakes.notifications.storage_root'), '/');

        $this->storage = $this->directory.'/serialized';

        (new Filesystem)->ensureDirectoryExists($this->directory);

        $this->loadNotifications();
    }

    public function cleanStorage()
    {
        (new Filesystem)->cleanDirectory($this->directory);
    }

    public function loadNotifications(): self
    {
        $this->notifications = file_exists($this->storage)
            ? rescue(fn () => unserialize(file_get_contents($this->storage)), [], false)
            : [];

        return $this;
    }

    public function sendNow($notifiables, $notification, array $channels = null)
    {
        parent::sendNow($notifiables, $notification, $channels);

        $this->storeNotifications();
    }

    private function storeNotifications()
    {
        (new Filesystem)->ensureDirectoryExists($this->directory);

        file_put_contents($this->storage, serialize($this->notifications));
    }
}
