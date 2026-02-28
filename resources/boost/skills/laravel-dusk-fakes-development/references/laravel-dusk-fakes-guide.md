# Laravel Dusk Fakes Reference

Complete reference for `protonemedia/laravel-dusk-fakes`.

Primary docs: https://github.com/protonemedia/laravel-dusk-fakes#readme

## Goal

In Laravel Dusk browser tests, your application code runs in a separate PHP process than the test assertions.

This package provides **persistent fakes** (Bus/Mail/Notifications/Queue) so you can:

- enable a fake inside the application process,
- perform browser actions,
- then assert dispatch/sent activity from the test.

## Installation

Dev dependency:

```bash
composer require protonemedia/laravel-dusk-fakes --dev
```

## Dusk environment handling

Each “persistent fake” is enabled via an environment flag that must be set in the Dusk environment.

Typically this means setting the env var in `.env.dusk.*` (per Laravel Dusk docs), not in your normal `.env`.

## Persistent Bus (queued jobs)

### Enable

Set in Dusk env:

```bash
DUSK_FAKE_BUS=true
```

### Use in a Dusk test

Add `PersistentBus` trait. You do **not** have to call `Bus::fake()` manually.

```php
use ProtoneMedia\LaravelDuskFakes\Bus\PersistentBus;

class OrderInvoiceTest extends DuskTestCase
{
    use PersistentBus;

    public function test_dispatch_invoice_job_after_confirming_order()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/order/1')
                ->press('Confirm')
                ->waitForText('We will generate an invoice!');

            Bus::assertDispatched(SendOrderInvoice::class);
        });
    }
}
```

### Only fake specific jobs

Allow other jobs to run normally, but persist-fake selected ones:

```php
Bus::jobsToFake(ShipOrder::class);

$browser->visit(...);

Bus::assertDispatched(SendOrderInvoice::class);
```

## Persistent Mails

### Enable

```bash
DUSK_FAKE_MAILS=true
```

### Use

```php
use ProtoneMedia\LaravelDuskFakes\Mails\PersistentMails;

class OrderConfirmTest extends DuskTestCase
{
    use PersistentMails;

    public function test_send_order_confirmed_mailable_to_user()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/order/1')
                ->press('Confirm')
                ->waitForText('We have emailed your order confirmation!');

            Mail::assertSent(OrderConfirmed::class, function ($mail) use ($user) {
                return $mail->hasTo($user->email);
            });
        });
    }
}
```

## Persistent Notifications

### Enable

```bash
DUSK_FAKE_NOTIFICATIONS=true
```

### Use

```php
use ProtoneMedia\LaravelDuskFakes\Notifications\PersistentNotifications;

class PasswordResetTest extends DuskTestCase
{
    use PersistentNotifications;

    public function test_reset_password_link_can_be_requested()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/forgot-password')
                ->type('email', $user->email)
                ->press('Email Password Reset Link')
                ->waitForText('We have emailed your password reset link!');

            Notification::assertSentTo($user, ResetPassword::class);
        });
    }
}
```

## Persistent Queue

### Enable

```bash
DUSK_FAKE_QUEUE=true
```

### Use

```php
use ProtoneMedia\LaravelDuskFakes\Queue\PersistentQueue;

class OrderInvoiceTest extends DuskTestCase
{
    use PersistentQueue;

    public function test_dispatch_invoice_job_after_confirming_order()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/order/1')
                ->press('Confirm')
                ->waitForText('We will generate an invoice!');

            Queue::assertDispatched(SendOrderInvoice::class);
        });
    }
}
```

### Only fake specific jobs

```php
Queue::jobsToFake(ShipOrder::class);

$browser->visit(...);

Queue::assertDispatched(SendOrderInvoice::class);
```

## Common patterns

- Prefer asserting *after* the UI action and after waiting for expected UI text/state (`waitForText`, etc.).
- If you use partial faking (`jobsToFake()`), keep the list explicit per test to avoid cross-test coupling.

## Pitfalls / gotchas

- **Env flags must be set for Dusk:** if the fake “does nothing”, verify `.env.dusk.*` contains the right `DUSK_FAKE_*` value.
- **App/test process boundary:** don’t rely on `Bus::fake()` in the test process; use these traits instead.
- **State leakage:** if the persistent fake stores state across requests, ensure tests clean up/avoid ordering dependence.
