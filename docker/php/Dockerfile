FROM php:8.2-apache

# Instal dependensi sistem + Node.js
RUN apt-get update && apt-get install -y \
    zip unzip git curl libpng-dev libonig-dev libxml2-dev \
    && curl -sL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd \
    && pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Aktifkan mod_rewrite untuk Laravel
RUN a2enmod rewrite

# Salin konfigurasi Apache
COPY docker/php/apache.conf /etc/apache2/sites-available/000-default.conf

# Salin entrypoint
COPY docker/php/init.sh /usr/local/bin/init.sh
RUN chmod +x /usr/local/bin/init.sh
ENTRYPOINT ["/usr/local/bin/init.sh"]
