<?php

namespace ProtoneMedia\LaravelDuskFakes\Bus;

use Illuminate\Bus\PendingBatch;
use Illuminate\Contracts\Bus\QueueingDispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Testing\Fakes\BusFake;
use Illuminate\Support\Testing\Fakes\PendingBatchFake;

class PersistentBusFake extends BusFake
{
    private string $directory;

    private string $storage;

    public function __construct(QueueingDispatcher $dispatcher, $jobsToFake = [])
    {
        parent::__construct($dispatcher, $jobsToFake);

        $this->directory = rtrim(config('dusk-fakes.bus.storage_root'), '/');

        $this->storage = $this->directory.'/serialized';

        (new Filesystem)->ensureDirectoryExists($this->directory);

        $this->loadBus();
    }

    public function jobsToFake($jobsToFake = [])
    {
        $this->jobsToFake = Arr::wrap($jobsToFake);

        $this->storeBus();
    }

    public function cleanStorage()
    {
        (new Filesystem)->cleanDirectory($this->directory);
    }

    public function loadBus(): self
    {
        $unserialized = file_exists($this->storage)
            ? rescue(fn () => unserialize(file_get_contents($this->storage)), [], false)
            : [];

        $this->jobsToFake = $unserialized['jobsToFake'] ?? [];
        $this->commands = $unserialized['commands'] ?? [];
        $this->commandsSync = $unserialized['commandsSync'] ?? [];
        $this->commandsAfterResponse = $unserialized['commandsAfterResponse'] ?? [];
        $this->batches = $unserialized['batches'] ?? [];

        return $this;
    }

    public function dispatch($command)
    {
        return tap(parent::dispatch($command), fn () => $this->storeBus());
    }

    public function dispatchSync($command, $handler = null)
    {
        return tap(parent::dispatchSync($command, $handler), fn () => $this->storeBus());
    }

    public function dispatchNow($command, $handler = null)
    {
        return tap(parent::dispatchNow($command, $handler), fn () => $this->storeBus());
    }

    public function dispatchToQueue($command)
    {
        return tap(parent::dispatchToQueue($command), fn () => $this->storeBus());
    }

    public function dispatchAfterResponse($command)
    {
        return tap(parent::dispatchAfterResponse($command), fn () => $this->storeBus());
    }

    public function recordPendingBatch(PendingBatch $pendingBatch)
    {
        return tap(parent::recordPendingBatch($pendingBatch), fn () => $this->storeBus());
    }

    public function cleanupCommand(array $jobs): array
    {
        return collect($jobs)->map(function ($job) {
            tap(invade($job), function ($job) {
                if (! $job->job) {
                    return;
                }

                $job = invade($job->job);
                $job->container = null;

                if (! $job->instance) {
                    return;
                }

                invade($job->instance)->container = null;
                invade($job->instance)->dispatcher = null;
            });

            return $job;
        })->all();
    }

    private function storeBus()
    {
        (new Filesystem)->ensureDirectoryExists($this->directory);

        file_put_contents($this->storage, serialize([
            'jobsToFake' => $this->jobsToFake,
            'commands' => collect($this->commands)->map([$this, 'cleanupCommand'])->all(),
            'commandsSync' => collect($this->commandsSync)->map([$this, 'cleanupCommand'])->all(),
            'commandsAfterResponse' => collect($this->commandsAfterResponse)->map([$this, 'cleanupCommand'])->all(),
            'batches' => collect($this->batches)->each(function (PendingBatchFake $batch) {
                tap(invade($batch), function ($batch) {
                    $batch->bus = null;
                });

                return $batch;
            })->all(),
        ]));
    }
}
