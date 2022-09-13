<?php

namespace ProtoneMedia\LaravelDuskFakes\Tests;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DummyJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, Batchable;
}
