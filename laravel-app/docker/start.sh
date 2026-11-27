#!/bin/sh
set -e

# Render memberi nomor port lewat variabel PORT (bawaan 10000)
PORT="${PORT:-10000}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

php artisan config:cache
php artisan route:cache
php artisan migrate --force

# Isi data contoh hanya kalau SEED_DATABASE=true (cukup untuk deploy pertama)
if [ "${SEED_DATABASE:-false}" = "true" ]; then
    php artisan db:seed --force || true
fi

exec apache2-foreground
