<?php

use Illuminate\Support\Facades\Notification;
use ProtoneMedia\LaravelDuskFakes\Notifications\PersistentNotificationFake;
use ProtoneMedia\LaravelDuskFakes\Notifications\PersistentNotifications;
use ProtoneMedia\LaravelDuskFakes\Notifications\UncachedPersistentNotificationFake;
use ProtoneMedia\LaravelDuskFakes\Tests\DummyNotification;
use ProtoneMedia\LaravelDuskFakes\Tests\DummyUser;

$dummyTest = new class
{
    use PersistentNotifications;
};

it('can persist sent notifications', function () use ($dummyTest) {
    expect(storage_path('framework/testing/notifications/serialized'))->not->toBeFile();

    expect(Notification::getFacadeRoot())->toBeInstanceOf(PersistentNotificationFake::class);

    $user = (new DummyUser)->forceFill(['id' => 1]);

    Notification::send($user, new DummyNotification);

    expect(storage_path('framework/testing/notifications/serialized'))->toBeFile();

    $dummyTest->setUpPersistentNotifications();

    expect(Notification::getFacadeRoot())->toBeInstanceOf(UncachedPersistentNotificationFake::class);

    Notification::assertSentToTimes($user, DummyNotification::class, 1);

    unlink(storage_path('framework/testing/notifications/serialized'));

    Notification::assertNothingSent();
});

afterEach(fn () => $dummyTest->tearDownPersistentNotifications());
