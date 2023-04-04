<?php

namespace ProtoneMedia\LaravelDuskFakes\Bus;

use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @mixin \Illuminate\Support\Testing\Fakes\BusFake
 */
class UncachedPersistentBusFake
{
    use ForwardsCalls;

    public function __construct(private PersistentBusFake $fake)
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
        return $this->forwardCallTo($this->fake->loadBus(), $method, $parameters);
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
