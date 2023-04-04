<?php

namespace ProtoneMedia\LaravelDuskFakes\Queue;

use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @mixin \Illuminate\Support\Testing\Fakes\QueueFake
 */
class UncachedPersistentQueueFake
{
    use ForwardsCalls;

    public function __construct(private PersistentQueueFake $fake)
    {
    }

    /**
     * Handle dynamic method calls into the fake.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->fake->loadQueue(), $method, $parameters);
    }

    /**
     * Handle dynamic static method calls into the fake.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public static function __callStatic($method, $parameters)
    {
        return app(static::class)->$method(...$parameters);
    }
}
