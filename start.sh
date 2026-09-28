#!/bin/sh
set -eu

PORT="${PORT:-8080}"
printf 'Listen %s\n' "$PORT" > /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

if [ -n "${MYSQL_ATTR_SSL_CA_BASE64:-}" ]; then
	printf '%s' "$MYSQL_ATTR_SSL_CA_BASE64" | base64 -d > /tmp/aiven-ca.pem
	export MYSQL_ATTR_SSL_CA=/tmp/aiven-ca.pem
fi

exec apache2-foreground