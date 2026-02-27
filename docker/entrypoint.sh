#!/usr/bin/env sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
  echo "WARNING: .env not found. Render should inject env vars, but local env file is missing."
fi

php artisan storage:link >/dev/null 2>&1 || true

if [ "${RUN_MIGRATIONS}" = "1" ]; then
  php artisan migrate --force
fi

php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache || true
php artisan view:cache || true

exec "$@"
