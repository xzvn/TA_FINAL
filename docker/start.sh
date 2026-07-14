#!/usr/bin/env bash
set -e

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

chmod -R 775 storage bootstrap/cache

php artisan optimize:clear || true
php artisan package:discover --ansi || true

if [ ! -e public/storage ]; then
    php artisan storage:link || true
else
    echo "public/storage already exists, skip storage:link"
fi

exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}