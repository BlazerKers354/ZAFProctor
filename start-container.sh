#!/bin/bash
set -e
php artisan migrate --force
php artisan storage:link --force 2>/dev/null || true
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
node /assets/scripts/prestart.mjs /app/Caddyfile /etc/caddy/Caddyfile
exec caddy run --config /etc/caddy/Caddyfile --adapter caddyfile
