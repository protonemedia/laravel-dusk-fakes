# laravel-dusk-fakes development guide

For full documentation, see the README: https://github.com/protonemedia/laravel-dusk-fakes#readme

## At a glance
Provides persistent fakes for Laravel Dusk (e.g., Bus/Mail/Notifications) so assertions work across browser process boundaries.

## Local setup
- Install dependencies: `composer install`
- Keep the dev loop package-focused (avoid adding app-only scaffolding).

## Testing
- Run: `composer test` (preferred) or the repository’s configured test runner.
- Add regression tests for bug fixes.

## Notes & conventions
- Dusk runs in a separate process: persistence and storage are core.
- Keep env-flag behavior stable (e.g., DUSK_FAKE_*).
- Add tests for persistence/cleanup and selective faking behavior.
