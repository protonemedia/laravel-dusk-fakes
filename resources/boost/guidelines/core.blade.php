{{-- Laravel Dusk Fakes Guidelines for AI Code Assistants --}}
{{-- Source: https://github.com/protonemedia/laravel-dusk-fakes --}}
{{-- License: MIT | (c) ProtoneMedia --}}

## Laravel Dusk Fakes

- `protonemedia/laravel-dusk-fakes` provides persistent fakes for Bus, Mail, Notifications, and Queue in Laravel Dusk browser tests, bridging the app/test process boundary.
- Always activate the `laravel-dusk-fakes-development` skill when working with persistent fakes in Dusk tests, or any code that uses the `PersistentBus`, `PersistentMails`, `PersistentNotifications`, or `PersistentQueue` traits.
