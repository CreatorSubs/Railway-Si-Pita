# Stage 1: Build Vite assets using Node.js
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY . .
RUN npm install && npm run build

# Stage 2: Production PHP server using Apache
FROM php:8.4-apache

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Salin hasil build Vite dari stage 1 ke public/build Laravel
COPY --from=node-builder /app/public/build /var/www/html/public/build

RUN composer install --no-dev --optimize-autoloader --ignore-platform-req=php

RUN chmod -R 777 storage bootstrap/cache

# Aktifkan mod_rewrite Apache untuk routing Laravel
RUN a2enmod rewrite

# Ubah Document Root Apache agar mengarah ke folder /public Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Berikan izin eksekusi pada start.sh
COPY start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8080

CMD ["/start.sh"]