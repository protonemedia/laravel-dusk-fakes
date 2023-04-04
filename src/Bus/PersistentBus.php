<?php

namespace ProtoneMedia\LaravelDuskFakes\Bus;

use Illuminate\Support\Facades\Bus;

trait PersistentBus
{
    public function setUpPersistentBus()
    {
        Bus::swap(
            app(UncachedPersistentBusFake::class)
        );
    }

    public function tearDownPersistentBus()
    {
        Bus::cleanStorage();
    }
}
