<?php

use Illuminate\Support\Facades\Mail;
use ProtoneMedia\LaravelDuskFakes\Mails\PersistentMailFake;
use ProtoneMedia\LaravelDuskFakes\Mails\PersistentMails;
use ProtoneMedia\LaravelDuskFakes\Mails\UncachedPersistentMailFake;
use ProtoneMedia\LaravelDuskFakes\Tests\DummyMail;

$dummyTest = new class
{
    use PersistentMails;
};

afterEach(fn () => $dummyTest->tearDownPersistentMails());

it('can persist sent mails', function () use ($dummyTest) {
    expect(Mail::getFacadeRoot())->toBeInstanceOf(PersistentMailFake::class);

    Mail::to('test@example.com')->send(new DummyMail);

    expect(storage_path('framework/testing/mails/serialized'))->toBeFile();

    $dummyTest->setUpPersistentMails();

    expect(Mail::getFacadeRoot())->toBeInstanceOf(UncachedPersistentMailFake::class);

    Mail::assertSent(function (DummyMail $mail) {
        return $mail->hasTo('test@example.com');
    });

    unlink(storage_path('framework/testing/mails/serialized'));

    Mail::assertNothingSent();
});

it('can persist queued mails', function () use ($dummyTest) {
    expect(Mail::getFacadeRoot())->toBeInstanceOf(PersistentMailFake::class);

    Mail::to('test@example.com')->queue(new DummyMail);

    expect(storage_path('framework/testing/mails/serialized'))->toBeFile();

    $dummyTest->setUpPersistentMails();

    expect(Mail::getFacadeRoot())->toBeInstanceOf(UncachedPersistentMailFake::class);

    Mail::assertQueued(function (DummyMail $mail) {
        return $mail->hasTo('test@example.com');
    });

    unlink(storage_path('framework/testing/mails/serialized'));

    Mail::assertNothingQueued();
});
