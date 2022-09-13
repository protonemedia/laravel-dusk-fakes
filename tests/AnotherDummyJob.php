<?php

namespace ProtoneMedia\LaravelDuskFakes\Tests;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AnotherDummyJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, Batchable;

    public bool $handled = false;

    public function handle()
    {
        $this->handled = true;
    }
}
