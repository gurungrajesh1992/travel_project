FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libicu-dev \
        libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Composer, taken from its official image rather than installed via script
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY php.ini /usr/local/etc/php/conf.d/99-custom.ini

WORKDIR /var/www/html
