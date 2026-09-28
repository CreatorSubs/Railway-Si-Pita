#!/usr/bin/env bash

echo "=== MEMULAI CONTAINER ==="

# Coba clear cache, tapi abaikan jika gagal
php artisan config:clear || true
php artisan cache:clear || true

# Gunakan port dari Railway atau default 8080
PORT="${PORT:-8080}"
echo "Mengatur port Apache ke: $PORT"

# Konfigurasi port Apache
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

echo "=== MENJALANKAN APACHE ==="
exec apache2-foreground