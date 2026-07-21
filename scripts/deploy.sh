#!/usr/bin/env bash
# Langkah deploy production untuk E-Questioner DISDIKAL.
# Jalankan dari root project di server, setelah .env production terisi lengkap.
#
# Pemakaian:
#   ./scripts/deploy.sh

set -euo pipefail

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_DIR"

echo "==> Install dependency (mode production)"
composer install --no-dev --optimize-autoloader

echo "==> Jalankan migration"
php artisan migrate --force

echo "==> Bersihkan cache lama sebelum rebuild"
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "==> Cache konfigurasi, rute, dan view"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Selesai. Cek storage/logs/laravel.log kalau ada yang aneh."
