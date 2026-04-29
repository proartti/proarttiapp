#!/usr/bin/env bash
set -euo pipefail

ENVIRONMENT="${1:-production}"

if [[ ! -f artisan ]]; then
  echo "artisan file not found in current directory"
  exit 1
fi

echo "Starting ${ENVIRONMENT} release"

php artisan down || true

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache || true

php artisan up

echo "Release completed for ${ENVIRONMENT}"
