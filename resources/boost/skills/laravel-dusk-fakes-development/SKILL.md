---
name: laravel-dusk-fakes-development
description: Build and work with protonemedia/laravel-dusk-fakes features including persistent Bus, Mail, Notification, and Queue fakes for Laravel Dusk browser tests, bridging the app/test process boundary.
license: MIT
metadata:
  author: ProtoneMedia
---

# Laravel Dusk Fakes Development

## Overview
Use protonemedia/laravel-dusk-fakes to assert dispatched jobs, sent mail, sent notifications, and queued jobs in Laravel Dusk browser tests. Provides persistent fakes that bridge the separate app and test PHP processes.

## When to Activate
- Activate when working with Bus, Mail, Notification, or Queue assertions in Laravel Dusk browser tests.
- Activate when code references `PersistentBus`, `PersistentMails`, `PersistentNotifications`, `PersistentQueue`, or `DUSK_FAKE_*` environment variables.
- Activate when the user wants to fake or assert dispatched/sent activity across the Dusk app/test process boundary.

## Scope
- In scope: persistent fakes for Bus, Mail, Notifications, and Queue in Dusk tests, environment configuration, partial faking, assertion patterns.
- Out of scope: general Laravel fakes outside Dusk, non-Laravel test frameworks.

## Workflow
1. Identify the task (setting up a persistent fake, writing assertions, partial faking, debugging env flags, etc.).
2. Read `references/laravel-dusk-fakes-guide.md` and focus on the relevant section.
3. Apply the patterns from the reference, keeping code minimal and Laravel-native.

## Core Concepts

### Persistent Bus

Add the trait and set the env flag; no need to call `Bus::fake()` manually:

```php
use ProtoneMedia\LaravelDuskFakes\Bus\PersistentBus;

class OrderTest extends DuskTestCase
{
    use PersistentBus;

    public function test_dispatch_job()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/order/1')
                ->press('Confirm')
                ->waitForText('Done');

            Bus::assertDispatched(SendInvoice::class);
        });
    }
}
```

### Persistent Mails

```php
use ProtoneMedia\LaravelDuskFakes\Mails\PersistentMails;

class MailTest extends DuskTestCase
{
    use PersistentMails;

    public function test_send_confirmation()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/order/1')->press('Confirm');
            Mail::assertSent(OrderConfirmed::class);
        });
    }
}
```

### Partial Faking

Fake only specific jobs while letting others run normally:

```php
Bus::jobsToFake(ShipOrder::class);

$browser->visit('/order/1')->press('Confirm');

Bus::assertDispatched(ShipOrder::class);
```

## Do and Don't

Do:
- Set `DUSK_FAKE_BUS=true`, `DUSK_FAKE_MAILS=true`, `DUSK_FAKE_NOTIFICATIONS=true`, or `DUSK_FAKE_QUEUE=true` in `.env.dusk.*`.
- Use the corresponding trait (`PersistentBus`, `PersistentMails`, `PersistentNotifications`, `PersistentQueue`) on the test class.
- Assert after browser actions and after waiting for expected UI state (`waitForText`, etc.).
- Use `jobsToFake()` for partial faking to avoid faking unrelated jobs.

Don't:
- Don't call `Bus::fake()` or `Mail::fake()` manually — the traits handle it.
- Don't set `DUSK_FAKE_*` flags in the normal `.env`; they belong in `.env.dusk.*`.
- Don't rely on assertion order across tests — persistent fakes may carry state if not cleaned up.

## References
- `references/laravel-dusk-fakes-guide.md`
