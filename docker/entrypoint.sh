#!/bin/sh

set -eu

APP_DIR=/var/www/html
ENV_FILE="${APP_DIR}/.env"

echo "Starting Photo Slideshow..."
echo ""

# Clear cached manifests that may reference dev-only providers or stale env.
rm -f "${APP_DIR}"/bootstrap/cache/*.php

# Create .env file from environment variables if it doesn't exist.
if [ ! -f "${ENV_FILE}" ]; then
    echo "Creating .env from environment..."
    cat > "${ENV_FILE}" << ENVEOF
APP_NAME=${APP_NAME:-PhotoSlideshow}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY:-}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost:2026}
DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST:-db}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-slideshow_db}
DB_USERNAME=${DB_USERNAME:-admin}
DB_PASSWORD=${DB_PASSWORD:-changeme}
SESSION_DRIVER=${SESSION_DRIVER:-database}
CACHE_STORE=${CACHE_STORE:-database}
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
FILESYSTEM_DISK=${FILESYSTEM_DISK:-local}
ENVEOF
    chown www-data:www-data "${ENV_FILE}"
    chmod 644 "${ENV_FILE}"
fi

# Wait for database.
echo "Waiting for database..."
MAX_TRIES=30
TRIES=0
until php -r "
try {
    \$host = getenv('DB_HOST') ?: 'db';
    \$port = getenv('DB_PORT') ?: '3306';
    \$user = getenv('DB_USERNAME') ?: 'admin';
    \$pass = getenv('DB_PASSWORD') ?: 'changeme';
    new PDO(\"mysql:host=\$host;port=\$port\", \$user, \$pass);
    exit(0);
} catch (Exception \$e) {
    exit(1);
}
" 2>/dev/null; do
    TRIES=$((TRIES + 1))
    if [ "${TRIES}" -ge "${MAX_TRIES}" ]; then
        echo "Database not available after ${MAX_TRIES} attempts. Starting anyway..."
        break
    fi
    sleep 2
done
echo "Database is ready."

# Resolve application key before caching config.
# A blank APP_KEY environment variable overrides .env in Laravel, so export the
# generated/file key explicitly for every artisan command and web request.
ENV_KEY="${APP_KEY:-}"
FILE_KEY=$(grep "^APP_KEY=" "${ENV_FILE}" | cut -d'=' -f2- || true)

if [ -n "${ENV_KEY}" ]; then
    EFFECTIVE_KEY="${ENV_KEY}"
elif [ -n "${FILE_KEY}" ]; then
    EFFECTIVE_KEY="${FILE_KEY}"
else
    echo "Generating application key..."
    EFFECTIVE_KEY="base64:$(php -r 'echo base64_encode(random_bytes(32));')"
fi

export APP_KEY="${EFFECTIVE_KEY}"

if grep -q "^APP_KEY=" "${ENV_FILE}"; then
    sed -i "s|^APP_KEY=.*|APP_KEY=${EFFECTIVE_KEY}|" "${ENV_FILE}"
else
    echo "APP_KEY=${EFFECTIVE_KEY}" >> "${ENV_FILE}"
fi

# Run migrations.
echo "Running migrations..."
php artisan migrate --force --no-interaction 2>&1 || echo "Migration warning (may be OK on first run)"

# Seed database (idempotent - uses firstOrCreate).
echo "Seeding database..."
php artisan db:seed --force --no-interaction 2>&1 || echo "Seed warning"

# Optimize for production.
echo "Optimizing..."
php artisan config:cache --no-interaction 2>&1
php artisan route:cache --no-interaction 2>&1
php artisan view:cache --no-interaction 2>&1

# Storage link (idempotent).
if [ -L "${APP_DIR}/public/storage" ]; then
    echo "Storage link already exists."
elif [ -e "${APP_DIR}/public/storage" ]; then
    echo "public/storage exists and is not a symlink; leaving it unchanged."
else
    php artisan storage:link --no-interaction 2>&1
fi

echo ""
echo "Application ready."
echo "   URL: http://localhost:2026"
echo "   Login: admin / password123"
echo ""

# Start supervisor (nginx + php-fpm).
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
