<?php

namespace ProtoneMedia\LaravelDuskFakes\Queue;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Testing\Fakes\QueueFake;

class PersistentQueueFake extends QueueFake
{
    private string $directory;

    private string $storage;

    public function __construct($app, $jobsToFake = [], $queue = null)
    {
        parent::__construct($app, $jobsToFake, $queue);

        $this->directory = rtrim(config('dusk-fakes.queue.storage_root'), '/');

        $this->storage = $this->directory.'/serialized';

        (new Filesystem)->ensureDirectoryExists($this->directory);

        $this->loadQueue();
    }

    public function jobsToFake($jobsToFake = [])
    {
        $this->jobsToFake = Collection::wrap($jobsToFake);

        $this->storeQueue();
    }

    public function cleanStorage()
    {
        (new Filesystem)->cleanDirectory($this->directory);
    }

    public function loadQueue(): self
    {
        $unserialized = file_exists($this->storage)
            ? rescue(fn () => unserialize(file_get_contents($this->storage)), [], false)
            : [];

        $this->jobsToFake = Collection::make($unserialized['jobsToFake'] ?? []);
        $this->jobsToBeQueued = Collection::make($unserialized['jobsToBeQueued'] ?? []);
        $this->jobs = $unserialized['jobs'] ?? [];

        return $this;
    }

    public function push($job, $data = '', $queue = null)
    {
        parent::push($job, $data, $queue);

        $this->storeQueue();
    }

    private function storeQueue()
    {
        (new Filesystem)->ensureDirectoryExists($this->directory);

        file_put_contents($this->storage, serialize([
            'jobsToFake' => $this->jobsToFake->all(),
            'jobsToBeQueued' => $this->jobsToBeQueued->all(),
            'jobs' => $this->jobs,
        ]));
    }
}
