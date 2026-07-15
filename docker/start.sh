#!/usr/bin/env bash
set -e

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

chmod -R 775 storage bootstrap/cache

php artisan optimize:clear || true
php artisan config:clear || true
php artisan view:clear || true
php artisan route:clear || true
php artisan package:discover --ansi || true

if [ ! -e public/storage ]; then
    php artisan storage:link || true
else
    echo "public/storage already exists, skip storage:link"
fi

echo "Checking public build assets..."
ls -la public || true
ls -la public/build || true
ls -la public/build/assets || true

echo "=== PHP Configuration ==="
php --ini

echo "upload_max_filesize:"
php -r "echo ini_get('upload_max_filesize') . PHP_EOL;"

echo "post_max_size:"
php -r "echo ini_get('post_max_size') . PHP_EOL;"

echo "memory_limit:"
php -r "echo ini_get('memory_limit') . PHP_EOL;"

exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}