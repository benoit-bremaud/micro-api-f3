FROM php:8.1-fpm

# Éviter les prompts debconf pendant le build
ENV DEBIAN_FRONTEND=noninteractive

# Outils & libs nécessaires pour intl/zip/sqlite + composer déjà présent
RUN apt-get update && apt-get install -y \
    git unzip pkg-config \
    libicu-dev libzip-dev \
    sqlite3 libsqlite3-dev \
 && docker-php-ext-install intl zip pdo_sqlite opcache \
 && docker-php-ext-enable opcache \
 && rm -rf /var/lib/apt/lists/*

# Composer (copié depuis l'image officielle)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
