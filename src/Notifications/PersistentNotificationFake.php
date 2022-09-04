<?php

namespace ProtoneMedia\LaravelDuskFakes\Notifications;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Testing\Fakes\NotificationFake;
use Symfony\Component\Finder\SplFileInfo;

class PersistentNotificationFake extends NotificationFake
{
    private string $storageDirectory;

    public function __construct()
    {
        $this->storageDirectory = config('dusk-fakes.notifications.storage_root');

        (new Filesystem)->ensureDirectoryExists($this->storageDirectory);

        $this->loadNotifications();
    }

    public function cleanStorage()
    {
        (new Filesystem)->cleanDirectory($this->storageDirectory);
    }

    public function loadNotifications(): self
    {
        $this->notifications = [];

        $persistentNotifications = (new Filesystem)->files($this->storageDirectory);

        collect($persistentNotifications)->each(function (SplFileInfo $file) {
            /** @var PersistentNotification $persistentNotification */
            $persistentNotification = unserialize(file_get_contents($file->getPathname()));

            $this->notifications[$persistentNotification->notifiableClass][$persistentNotification->notifiableKey][$persistentNotification->notificationClass][] = $persistentNotification->notification;
        });

        return $this;
    }

    public function sendNow($notifiables, $notification, array $channels = null)
    {
        parent::sendNow($notifiables, $notification, $channels);

        $this->storeNotifications();
    }

    private function storeNotifications()
    {
        $key = 0;

        foreach ($this->notifications as $notifiableClass => $notifiableKeys) {
            foreach ($notifiableKeys as $notifiableKey => $notificationsByClass) {
                foreach ($notificationsByClass as $notificationClass => $notifications) {
                    foreach ($notifications as $notification) {
                        $serializable = new PersistentNotification($notifiableClass, $notifiableKey, $notificationClass, $notification);

                        file_put_contents(
                            $this->storageDirectory.'/'.$key,
                            serialize($serializable)
                        );

                        $key++;
                    }
                }
            }
        }
    }
}
