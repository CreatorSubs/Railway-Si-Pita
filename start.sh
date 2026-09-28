#!/usr/bin/env bash

# Bersihkan cache agar konfigurasi terbaru terbaca
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Jalankan migrasi database (opsional, lewati jika gagal agar container tidak mati)
php artisan migrate --force || true

# Ambil port dari environment variable PORT yang diberikan Railway
PORT="${PORT:-8080}"
echo "Mengatur Apache agar berjalan pada port $PORT..."

# Ubah port default Apache (80) ke port dinamis Railway
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

# Jalankan Apache sebagai proses utama container
exec apache2-foreground