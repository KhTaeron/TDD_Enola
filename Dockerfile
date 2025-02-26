FROM php:8.2-fpm

# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libonig-dev \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql mbstring

WORKDIR /var/www

CMD ["php-fpm"]
