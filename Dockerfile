# Stage 1: Build Vite assets using Node.js
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

# Stage 2: Production PHP server using Apache
FROM php:8.4-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    libpng-dev \
    libxml2-dev \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" bcmath exif gd mbstring pdo_mysql pcntl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

COPY --from=frontend /app/public/build /var/www/html/public/build

RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
    && php artisan storage:link --force \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwX storage bootstrap/cache

# Pastikan PHP membaca environment variable proses (EGPCS)
RUN printf 'variables_order = "EGPCS"\n' > /usr/local/etc/php/conf.d/docker-php-variables-order.ini

# Aktifkan mod_rewrite Apache, PassEnv, dan aturan Laravel pada document root.
RUN a2enmod rewrite \
    && printf '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\nPassEnv APP_ENV APP_KEY APP_DEBUG APP_URL DB_CONNECTION DB_HOST DB_PORT DB_DATABASE DB_USERNAME DB_PASSWORD MYSQLHOST MYSQLPORT MYSQLDATABASE MYSQLUSER MYSQLPASSWORD MYSQL_URL SESSION_DRIVER CACHE_STORE LOG_CHANNEL FILESYSTEM_DISK MYSQL_ATTR_SSL_CA\n' > /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Ubah Document Root Apache agar mengarah ke folder /public Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
ENV LOG_CHANNEL=stderr
ENV PORT=8080

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf

COPY start.sh /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container

EXPOSE 8080

CMD ["/usr/local/bin/start-container"]