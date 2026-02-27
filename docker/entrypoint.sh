#!/usr/bin/env sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
  echo "WARNING: .env not found. Render should inject env vars, but local env file is missing."
fi

# Optional: inject custom MySQL CA from env (Aiven, etc.)
if [ -n "${MYSQL_ATTR_SSL_CA_BASE64}" ]; then
  echo "${MYSQL_ATTR_SSL_CA_BASE64}" | base64 -d > /etc/ssl/certs/aiven-ca.crt
  export MYSQL_ATTR_SSL_CA=/etc/ssl/certs/aiven-ca.crt
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
