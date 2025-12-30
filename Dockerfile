# PHP 8.2 avec Apache (mod_php, PAS CGI)
FROM php:8.2-apache

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libicu-dev \
    libzip-dev \
    && docker-php-ext-install \
    intl \
    pdo \
    pdo_mysql \
    zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var public

RUN composer config --no-plugins allow-plugins.symfony/flex true

ENV COMPOSER_MEMORY_LIMIT=-1

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader

RUN sed -i 's|/var/www/html|/var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

RUN rm -f /etc/apache2/conf-enabled/php*.conf

EXPOSE 80
CMD ["apache2-foreground"]
