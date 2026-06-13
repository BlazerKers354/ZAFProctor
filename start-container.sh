#!/bin/bash
set -e

# Wait for MySQL to be ready (max 60 seconds)
echo "Waiting for MySQL to be ready..."
for i in $(seq 1 30); do
    if php artisan db:monitor --no-ansi 2>/dev/null | grep -q "OK\|reachable"; then
        echo "MySQL is ready!"
        break
    fi
    # Fallback: try a simple PDO connection check
    if php -r "try { new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD')); echo 'OK'; } catch(Exception \$e) { exit(1); }" 2>/dev/null; then
        echo "MySQL is ready!"
        break
    fi
    echo "MySQL not ready yet, waiting... ($i/30)"
    sleep 2
done

# Run database migrations
php artisan migrate --force

# Recreate storage symlink
php artisan storage:link --force 2>/dev/null || true

# Clear and rebuild runtime caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Start FrankenPHP (Caddy)
node /assets/scripts/prestart.mjs /app/Caddyfile /etc/caddy/Caddyfile
exec caddy run --config /etc/caddy/Caddyfile --adapter caddyfile
