# Laravel Dusk Fakes

[![Latest Version on Packagist](https://img.shields.io/packagist/v/protonemedia/laravel-dusk-fakes.svg?style=flat-square)](https://packagist.org/packages/protonemedia/laravel-dusk-fakes)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/protonemedia/laravel-dusk-fakes/run-tests?label=tests)](https://github.com/protonemedia/laravel-dusk-fakes/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/protonemedia/laravel-dusk-fakes.svg?style=flat-square)](https://packagist.org/packages/protonemedia/laravel-dusk-fakes)
[![Buy us a tree](https://img.shields.io/badge/Treeware-%F0%9F%8C%B3-lightgreen)](https://plant.treeware.earth/protonemedia/laravel-dusk-fakes)

## Sponsor this package!

❤️ We proudly support the community by developing Laravel packages and giving them away for free. If this package saves you time or if you're relying on it professionally, please consider [sponsoring the maintenance and development](https://github.com/sponsors/pascalbaljet). Keeping track of issues and pull requests takes time, but we're happy to help!

## Laravel Splade

**Did you hear about Laravel Splade? 🤩**

It's the *magic* of Inertia.js with the *simplicity* of Blade. [Splade](https://github.com/protonemedia/laravel-splade) provides a super easy way to build Single Page Applications using Blade templates. Besides that magic SPA-feeling, it comes with more than ten components to sparkle your app and make it interactive, all without ever leaving Blade.

## Installation

You can install the package via composer:

```bash
composer require protonemedia/laravel-dusk-fakes --dev
```

### Persist Bus (queued jobs)

Make sure you've set the `DUSK_FAKE_BUS` environment variable to `true` in the [Dusk environment](https://laravel.com/docs/9.x/dusk#environment-handling).

Finally, add the `PersistentBus` trait to your test. You don't have to manually call the `fake()` method on the `Bus` facade.

```php
<?php

namespace Tests\Browser\Auth;

use App\Jobs\SendOrderInvoice;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Laravel\Dusk\Browser;
use ProtoneMedia\LaravelDuskFakes\Bus\PersistentBus;
use Tests\DuskTestCase;

class OrderInvoiceTest extends DuskTestCase
{
    use DatabaseMigrations;
    use PersistentBus;

    public function test_dispatch_invoice_job_after_confirming_order()
    {
        $this->browse(function (Browser $browser) {
            $order = Order::factory()->create();

            $browser->visit('/order/'.$order->id)
                ->press('Confirm')
                ->waitForText('We will generate an invoice!');

            Bus::assertDispatched(SendOrderInvoice::class);
        });
    }
}
```

If you only need to fake specific jobs while allowing your other jobs to execute normally, you may pass the class names of the jobs that should be faked to the `jobsToFake()` method:

```php
Bus::jobsToFake(ShipOrder::class);

$browser->visit(...);

Bus::assertDispatched(SendOrderInvoice::class);
```

### Persist Mails

Make sure you've set the `DUSK_FAKE_MAILS` environment variable to `true` in the [Dusk environment](https://laravel.com/docs/9.x/dusk#environment-handling).

Finally, add the `PersistentMails` trait to your test. You don't have to manually call the `fake()` method on the `Mail` facade.

```php
<?php

namespace Tests\Browser\Auth;

use App\Mail\OrderConfirmed;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Laravel\Dusk\Browser;
use ProtoneMedia\LaravelDuskFakes\Mails\PersistentMails;
use Tests\DuskTestCase;

class OrderConfirmTest extends DuskTestCase
{
    use DatabaseMigrations;
    use PersistentMails;

    public function test_send_order_confirmed_mailable_to_user()
    {
        $this->browse(function (Browser $browser) {
            $order = Order::factory()->create();

            $browser->visit('/order/'.$order->id)
                ->press('Confirm')
                ->waitForText('We have emailed your order confirmation!');

            Mail::assertSent(OrderConfirmed::class, function ($mail) use ($user) {
                return $mail->hasTo($user->email);
            });
        });
    }
}
```

### Persist Notifications

Make sure you've set the `DUSK_FAKE_NOTIFICATIONS` environment variable to `true` in the [Dusk environment](https://laravel.com/docs/9.x/dusk#environment-handling).

Finally, add the `PersistentNotifications` trait to your test. You don't have to manually call the `fake()` method on the `Notification` facade.

```php
<?php

namespace Tests\Browser\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Notification;
use Laravel\Dusk\Browser;
use ProtoneMedia\LaravelDuskFakes\Notifications\PersistentNotifications;
use Tests\DuskTestCase;

class PasswordResetTest extends DuskTestCase
{
    use DatabaseMigrations;
    use PersistentNotifications;

    public function test_reset_password_link_can_be_requested()
    {
        $this->browse(function (Browser $browser) {
            $user = User::factory()->create();

            $browser->visit('/forgot-password')
                ->type('email', $user->email)
                ->press('Email Password Reset Link')
                ->waitForText('We have emailed your password reset link!');

            Notification::assertSentTo($user, ResetPassword::class);
        });
    }
}
```

### Persist Queue

Make sure you've set the `DUSK_FAKE_QUEUE` environment variable to `true` in the [Dusk environment](https://laravel.com/docs/9.x/dusk#environment-handling).

Finally, add the `PersistentQueue` trait to your test. You don't have to manually call the `fake()` method on the `Queue` facade.

```php
<?php

namespace Tests\Browser\Auth;

use App\Jobs\SendOrderInvoice;
use App\Models\Order;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Mail;
use Laravel\Dusk\Browser;
use ProtoneMedia\LaravelDuskFakes\Queue\PersistentQueue;
use Tests\DuskTestCase;

class OrderInvoiceTest extends DuskTestCase
{
    use DatabaseMigrations;
    use PersistentQueue;

    public function test_dispatch_invoice_job_after_confirming_order()
    {
        $this->browse(function (Browser $browser) {
            $order = Order::factory()->create();

            $browser->visit('/order/'.$order->id)
                ->press('Confirm')
                ->waitForText('We will generate an invoice!');

            Queue::assertDispatched(SendOrderInvoice::class);
        });
    }
}
```

If you only need to fake specific jobs while allowing your other jobs to execute normally, you may pass the class names of the jobs that should be faked to the `jobsToFake()` method:

```php
Queue::jobsToFake(ShipOrder::class);

$browser->visit(...);

Queue::assertDispatched(SendOrderInvoice::class);
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Other Laravel packages

* [`Laravel Analytics Event Tracking`](https://github.com/protonemedia/laravel-analytics-event-tracking): Laravel package to easily send events to Google Analytics.
* [`Laravel Blade On Demand`](https://github.com/protonemedia/laravel-blade-on-demand): Laravel package to compile Blade templates in memory.
* [`Laravel Cross Eloquent Search`](https://github.com/protonemedia/laravel-cross-eloquent-search): Laravel package to search through multiple Eloquent models.
* [`Laravel Eloquent Scope as Select`](https://github.com/protonemedia/laravel-eloquent-scope-as-select): Stop duplicating your Eloquent query scopes and constraints in PHP. This package lets you re-use your query scopes and constraints by adding them as a subquery.
* [`Laravel Eloquent Where Not`](https://github.com/protonemedia/laravel-eloquent-where-not): This Laravel package allows you to flip/invert an Eloquent scope, or really any query constraint.
* [`Laravel Form Components`](https://github.com/protonemedia/laravel-form-components): Blade components to rapidly build forms with Tailwind CSS Custom Forms and Bootstrap 4. Supports validation, model binding, default values, translations, includes default vendor styling and fully customizable!
* [`Laravel MinIO Testing Tools`](https://github.com/protonemedia/laravel-minio-testing-tools): This package provides a trait to run your tests against a MinIO S3 server.
* [`Laravel Mixins`](https://github.com/protonemedia/laravel-mixins): A collection of Laravel goodies.
* [`Laravel Paddle`](https://github.com/protonemedia/laravel-paddle): Paddle.com API integration for Laravel with support for webhooks/events.
* [`Laravel Splade`](https://github.com/protonemedia/laravel-splade): Splade provides a super easy way to build Single Page Applications using Blade templates. Besides that magic SPA-feeling, it comes with more than ten components to sparkle your app and make it interactive, all without ever leaving Blade.
* [`Laravel Verify New Email`](https://github.com/protonemedia/laravel-verify-new-email): This package adds support for verifying new email addresses: when a user updates its email address, it won't replace the old one until the new one is verified.
* [`Laravel WebDAV`](https://github.com/protonemedia/laravel-webdav): WebDAV driver for Laravel's Filesystem.
* [`Laravel XSS Protection Middleware`](https://github.com/protonemedia/laravel-xss-protection): Laravel Middleware to protect your app against Cross-site scripting (XSS). It sanitizes request input by utilising the Laravel Security package, and it can sanatize Blade echo statements as well.

## Security

If you discover any security related issues, please email pascal@protone.media instead of using the issue tracker.

## Credits

- [Pascal Baljet](https://github.com/protonemedia)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
