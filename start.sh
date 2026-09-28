#!/usr/bin/env bash

echo "--- Memulai start.sh ---"

# Bersihkan cache agar konfigurasi terbaru terbaca
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "--- Menjalankan migrasi database ---"
# Gunakan || true agar jika migrasi gagal (misal salah password DB), container tidak langsung mati
php artisan migrate --force || echo "Migrasi dilewati atau gagal"

# Ambil port dari environment variable PORT yang diberikan Railway
PORT="${PORT:-8080}"
echo "Mengatur Apache agar berjalan pada port $PORT..."

# Ubah port default Apache (80) ke port dinamis Railway
sed -i "s/Listen 80/Listen ${PORT}/g" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-available/000-default.conf

echo "--- Menjalankan Apache ---"
# Jalankan Apache sebagai proses utama container
exec apache2-foreground