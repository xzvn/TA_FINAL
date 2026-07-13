#!/usr/bin/env bash
set -e

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

php artisan optimize:clear || true
php artisan package:discover --ansi || true
php artisan storage:link || true

apache2-foreground
