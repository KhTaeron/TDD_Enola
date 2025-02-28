FROM php:8.2-fpm

COPY --from=composer:2.1.9 /usr/bin/composer /usr/bin/composer

# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libonig-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mbstring

WORKDIR /var/www

CMD ["php-fpm"]
