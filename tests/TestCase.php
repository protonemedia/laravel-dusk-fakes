<?php

namespace ProtoneMedia\LaravelDuskFakes\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use ProtoneMedia\LaravelDuskFakes\LaravelDuskFakesServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'ProtoneMedia\\LaravelDuskFakes\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelDuskFakesServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('dusk-fakes.notifications.enabled', true);

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-dusk-fakes_table.php.stub';
        $migration->up();
        */
    }
}
