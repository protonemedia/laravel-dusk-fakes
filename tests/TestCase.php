<?php

namespace ProtoneMedia\LaravelDuskFakes\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use ProtoneMedia\LaravelDuskFakes\LaravelDuskFakesServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LaravelDuskFakesServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('dusk-fakes.notifications.enabled', true);
    }
}