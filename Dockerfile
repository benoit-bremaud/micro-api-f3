# Utiliser l'image officielle PHP 8.1 FPM comme base
FROM php:8.1-fpm

# Installer les extensions nécessaires (pdo_sqlite, intl, zip, opcache, etc.)
RUN apt-get update && apt-get install -y \
        libicu-dev libzip-dev unzip && \
    docker-php-ext-install pdo_sqlite intl zip opcache && \
    docker-php-ext-enable opcache

# Installer Composer dans l'image (on copie l'exécutable depuis l'image composer officielle)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
