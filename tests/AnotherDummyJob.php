<?php

namespace ProtoneMedia\LaravelDuskFakes\Tests;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AnotherDummyJob implements ShouldQueue
{
    use Batchable, InteractsWithQueue, Queueable;

    public function handle()
    {
        $this->handled = true;
    }
}
