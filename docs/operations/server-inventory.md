# Server Inventory

Last updated: 2026-04-28

## Hosting

- Provider: DreamHost
- Access: SSH available
- Cron: Available via DreamHost dashboard

## Runtime

- Target runtime on host: PHP 8.2.29 (changeable if needed)
- Local PHP CLI: 8.2.30 (installed via winget)
- Local Composer: 2.9.7 (installed as `composer.phar` in user bin)
- Local Node.js: 22.22.2
- Local npm: 10.9.7
- Local Laravel framework: 12.58.0

## Local Runtime Notes

- PHP extensions enabled locally for framework bootstrap: `openssl`, `zip`, `mbstring`, `fileinfo`, `pdo_mysql`, `pdo_sqlite`, `curl`, `intl`, `bcmath`, `sqlite3`.
- Initial scaffold speed issue was caused by `zip` being disabled; enabling it restored normal Composer dist install behavior.

## Database

- Engine: MySQL
- Host: mace.iad1-mysql-e2-6b.dreamhost.com
- Transport: TCP/IP
- Server version: 8.0.41-0ubuntu0.24.04.1
- Protocol version: 10
- Charset: UTF-8 Unicode (utf8mb3)

## Pending Technical Confirmation

- Deployment user and absolute deploy path on DreamHost
- Web root and domain mapping for staging and production
- Composer and Node availability on DreamHost host
- Email transport details for magic-link authentication
- Queue execution strategy under DreamHost constraints
