#!/usr/bin/env bash

echo "=== MEMULAI CONTAINER ==="

# Gunakan port 8080 secara paksa agar sesuai dengan settingan Networking Railway
PORT="8080"
echo "Menggunakan port: $PORT"

# Atur ulang konfigurasi port Apache
echo "Listen $PORT" > /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Bersihkan cache
php artisan config:clear || true
php artisan cache:clear || true

echo "=== MENJALANKAN APACHE ==="
exec apache2-foreground