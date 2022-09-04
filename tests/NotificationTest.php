<?php

use Illuminate\Support\Facades\Notification;
use ProtoneMedia\LaravelDuskFakes\Notifications\PersistentNotificationFake;
use ProtoneMedia\LaravelDuskFakes\Notifications\PersistentNotifications;
use ProtoneMedia\LaravelDuskFakes\Tests\DummyNotification;
use ProtoneMedia\LaravelDuskFakes\Tests\DummyUser;

$dummyTest = new class {
    use PersistentNotifications;
};

it('can persist sent notifications', function () use ($dummyTest) {
    expect(storage_path('framework/testing/notifications/0'))->not->toBeFile();

    expect(Notification::getFacadeRoot())->toBeInstanceOf(PersistentNotificationFake::class);

    $user = (new DummyUser)->forceFill(['id' => 1]);

    Notification::send($user, new DummyNotification);

    expect(storage_path('framework/testing/notifications/serialized'))->toBeFile();

    $dummyTest->setUpPersistentNotifications();

    Notification::assertSentToTimes($user, DummyNotification::class, 1);
});

afterEach(fn () => $dummyTest->tearDownPersistentNotifications());
