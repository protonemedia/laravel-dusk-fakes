<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Queue;
use ProtoneMedia\LaravelDuskFakes\Queue\PersistentQueue;
use ProtoneMedia\LaravelDuskFakes\Queue\PersistentQueueFake;
use ProtoneMedia\LaravelDuskFakes\Queue\UncachedPersistentQueueFake;
use ProtoneMedia\LaravelDuskFakes\Tests\AnotherDummyJob;
use ProtoneMedia\LaravelDuskFakes\Tests\DummyJob;

$dummyTest = new class
{
    use PersistentQueue;
};

afterEach(fn () => (new Filesystem)->cleanDirectory(storage_path('framework/testing')));

it('can persist a queued job', function () use ($dummyTest) {
    expect(Queue::getFacadeRoot())->toBeInstanceOf(PersistentQueueFake::class);

    Queue::push(new DummyJob);

    expect(storage_path('framework/testing/queue/serialized'))->toBeFile();

    $dummyTest->setUpPersistentQueue();

    expect(Queue::getFacadeRoot())->toBeInstanceOf(UncachedPersistentQueueFake::class);

    Queue::assertPushed(DummyJob::class);

    unlink(storage_path('framework/testing/queue/serialized'));

    Queue::assertNotPushed(DummyJob::class);
});

it('can persist a specific queued job', function () use ($dummyTest) {
    expect(Queue::getFacadeRoot())->toBeInstanceOf(PersistentQueueFake::class);

    Queue::jobsToFake(DummyJob::class);

    Queue::push(new AnotherDummyJob);
    Queue::push(new DummyJob);

    expect(storage_path('framework/testing/queue/serialized'))->toBeFile();

    $dummyTest->setUpPersistentQueue();

    expect(Queue::getFacadeRoot())->toBeInstanceOf(UncachedPersistentQueueFake::class);

    Queue::assertPushed(DummyJob::class);
    Queue::assertNotPushed(AnotherDummyJob::class);

    unlink(storage_path('framework/testing/queue/serialized'));
});
