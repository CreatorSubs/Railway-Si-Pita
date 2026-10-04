#!/bin/sh
set -eu

PORT="${PORT:-8080}"
printf 'Listen %s\n' "$PORT" > /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

# Decode Aiven CA SSL certificate if provided
if [ -n "${MYSQL_ATTR_SSL_CA_BASE64:-}" ]; then
	printf '%s' "$MYSQL_ATTR_SSL_CA_BASE64" | base64 -d > /tmp/aiven-ca.pem
	chmod 644 /tmp/aiven-ca.pem
	export MYSQL_ATTR_SSL_CA=/tmp/aiven-ca.pem
fi

# Ensure storage directories exist
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/storage/app/public \
         /var/www/html/bootstrap/cache

touch /var/www/html/storage/logs/laravel.log

# Re-link storage just in case a volume was mounted
php artisan storage:link --force || true

# Run database migrations if DB is configured (non-fatal if DB is temporarily unreachable)
DB_TARGET="${DB_HOST:-${MYSQLHOST:-}}"
if [ -n "$DB_TARGET" ] || [ -n "${MYSQL_URL:-}" ] || [ -n "${DB_URL:-}" ]; then
    echo "=== Running database migrations ==="
    php artisan migrate --force || echo "Warning: Migration failed. Please verify DB connection settings."
fi

# Seed Owner account if OWNER_PASSWORD is set and DB is configured
if [ -n "${OWNER_PASSWORD:-}" ] && { [ -n "$DB_TARGET" ] || [ -n "${MYSQL_URL:-}" ] || [ -n "${DB_URL:-}" ]; }; then
    echo "=== Seeding Owner account ==="
    php artisan db:seed --force || echo "Warning: Seeding failed."
fi

# Pastikan izin akses benar untuk www-data setelah operasi root
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 777 /var/www/html/storage /var/www/html/bootstrap/cache
chmod 666 /var/www/html/storage/logs/laravel.log

# Paksa matikan modul MPM yang bentrok dan aktifkan mpm_prefork secara bersih
a2dismod -f mpm_event mpm_worker mpm_prefork 2>/dev/null || true
a2enmod mpm_prefork

echo "=== Starting Apache web server on port ${PORT} ==="
exec apache2-foreground