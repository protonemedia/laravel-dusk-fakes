<?php

namespace ProtoneMedia\LaravelDuskFakes\Queue;

use Illuminate\Support\Facades\Queue;

trait PersistentQueue
{
    public function setUpPersistentQueue()
    {
        Queue::swap(
            app(UncachedPersistentQueueFake::class)
        );
    }

    public function tearDownPersistentQueue()
    {
        Queue::cleanStorage();
    }
}
