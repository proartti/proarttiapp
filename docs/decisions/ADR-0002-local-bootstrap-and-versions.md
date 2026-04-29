# ADR-0002: Local Bootstrap Runtime and Version Baseline

- Status: Accepted
- Date: 2026-04-28

## Context

The workstation initially lacked PHP and Composer, which blocked Laravel bootstrap. The project also needs compatibility with DreamHost PHP 8.2.x.

## Decision

Use local PHP 8.2.30 and Composer 2.9.7 to bootstrap Laravel 12.x and React/Inertia scaffolding.

## Consequences

- Laravel 13 is not selected because it requires PHP 8.3+.
- Local setup stays compatible with DreamHost PHP 8.2.29 constraints.
- Composer is installed user-local (`composer.phar`) and invoked through a local `composer.bat` shim for tooling compatibility.

## Notes

- Bootstrap operations require PHP extensions: openssl, zip, mbstring, fileinfo, pdo_mysql, pdo_sqlite, curl, intl, bcmath, sqlite3.
- Keep this version baseline until hosting runtime changes.
