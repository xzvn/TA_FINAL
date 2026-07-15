FROM node:20-alpine AS assets

WORKDIR /app

COPY package*.json ./
COPY vite.config.* ./
COPY tailwind.config.* ./
COPY postcss.config.* ./
COPY resources ./resources
COPY public ./public

RUN if [ -f package-lock.json ]; then npm ci; else npm install; fi && npm run build


FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

COPY . .
RUN composer dump-autoload --optimize


FROM php:8.3-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip gd bcmath exif opcache curl \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN printf "upload_max_filesize=20M\n\
    post_max_size=24M\n\
    memory_limit=256M\n\
    max_execution_time=120\n\
    max_input_time=120\n" \
    > /usr/local/etc/php/conf.d/uploads.ini

COPY --from=vendor /app /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build

COPY docker/start.sh /usr/local/bin/start.sh

RUN sed -i 's/\r$//' /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh \
    && mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

RUN printf "upload_max_filesize=20M\n\
    post_max_size=24M\n\
    memory_limit=256M\n\
    max_execution_time=120\n\
    max_input_time=120\n" \
    > /usr/local/etc/php/conf.d/uploads.ini

CMD ["start.sh"]