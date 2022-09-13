<?php

use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Testing\Fakes\PendingBatchFake;
use ProtoneMedia\LaravelDuskFakes\Bus\PersistentBus;
use ProtoneMedia\LaravelDuskFakes\Bus\PersistentBusFake;
use ProtoneMedia\LaravelDuskFakes\Bus\UncachedPersistentBusFake;
use ProtoneMedia\LaravelDuskFakes\Tests\AnotherDummyJob;
use ProtoneMedia\LaravelDuskFakes\Tests\DummyJob;

$dummyTest = new class
{
    use PersistentBus;
};

afterEach(fn () => $dummyTest->tearDownPersistentBus());

it('can persist a queued job', function () use ($dummyTest) {
    expect(storage_path('framework/testing/bus/serialized'))->not->toBeFile();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(PersistentBusFake::class);

    Bus::dispatch(new DummyJob);

    expect(storage_path('framework/testing/bus/serialized'))->toBeFile();

    $dummyTest->setUpPersistentBus();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(UncachedPersistentBusFake::class);

    Bus::assertDispatched(DummyJob::class);

    unlink(storage_path('framework/testing/bus/serialized'));

    Bus::assertNotDispatched(DummyJob::class);
});

it('can persist a specific queued job', function () use ($dummyTest) {
    expect(storage_path('framework/testing/bus/serialized'))->not->toBeFile();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(PersistentBusFake::class);

    Bus::jobsToFake(DummyJob::class);

    Bus::dispatch(new AnotherDummyJob);
    Bus::dispatch(new DummyJob);

    expect(storage_path('framework/testing/bus/serialized'))->toBeFile();

    $dummyTest->setUpPersistentBus();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(UncachedPersistentBusFake::class);

    Bus::assertDispatched(DummyJob::class);
    Bus::assertNotDispatched(AnotherDummyJob::class);
});

it('can persist a queued job using the sync method', function () use ($dummyTest) {
    expect(storage_path('framework/testing/bus/serialized'))->not->toBeFile();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(PersistentBusFake::class);

    Bus::dispatchSync(new DummyJob);

    expect(storage_path('framework/testing/bus/serialized'))->toBeFile();

    $dummyTest->setUpPersistentBus();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(UncachedPersistentBusFake::class);

    Bus::assertDispatchedSync(DummyJob::class);

    unlink(storage_path('framework/testing/bus/serialized'));

    Bus::assertNotDispatchedSync(DummyJob::class);
});

it('can persist a queued job using the now method', function () use ($dummyTest) {
    expect(storage_path('framework/testing/bus/serialized'))->not->toBeFile();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(PersistentBusFake::class);

    Bus::dispatchNow(new DummyJob);

    expect(storage_path('framework/testing/bus/serialized'))->toBeFile();

    $dummyTest->setUpPersistentBus();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(UncachedPersistentBusFake::class);

    Bus::assertDispatched(DummyJob::class);

    unlink(storage_path('framework/testing/bus/serialized'));

    Bus::assertNotDispatched(DummyJob::class);
});

it('can persist a queued job using the toQueue method', function () use ($dummyTest) {
    expect(storage_path('framework/testing/bus/serialized'))->not->toBeFile();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(PersistentBusFake::class);

    Bus::dispatchToQueue(new DummyJob);

    expect(storage_path('framework/testing/bus/serialized'))->toBeFile();

    $dummyTest->setUpPersistentBus();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(UncachedPersistentBusFake::class);

    Bus::assertDispatched(DummyJob::class);

    unlink(storage_path('framework/testing/bus/serialized'));

    Bus::assertNotDispatched(DummyJob::class);
});

it('can persist a queued job using the afterResponse method', function () use ($dummyTest) {
    expect(storage_path('framework/testing/bus/serialized'))->not->toBeFile();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(PersistentBusFake::class);

    dispatch(new DummyJob)->afterResponse();

    expect(storage_path('framework/testing/bus/serialized'))->toBeFile();

    $dummyTest->setUpPersistentBus();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(UncachedPersistentBusFake::class);

    Bus::assertDispatchedAfterResponse(DummyJob::class);

    unlink(storage_path('framework/testing/bus/serialized'));

    Bus::assertNotDispatchedAfterResponse(DummyJob::class);
});

it('can persist a queued batch', function () use ($dummyTest) {
    expect(storage_path('framework/testing/bus/serialized'))->not->toBeFile();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(PersistentBusFake::class);

    Bus::batch([
        new DummyJob,
        new DummyJob,
    ])->dispatch();

    expect(storage_path('framework/testing/bus/serialized'))->toBeFile();

    $dummyTest->setUpPersistentBus();

    expect(Bus::getFacadeRoot())->toBeInstanceOf(UncachedPersistentBusFake::class);

    Bus::assertBatchCount(1);
    Bus::assertBatched(function (PendingBatchFake $batch) {
        return  $batch->jobs->count() === 2;
    });

    unlink(storage_path('framework/testing/bus/serialized'));

    Bus::assertBatchCount(0);
});
