# PHP 8.2 avec Apache (mod_php, PAS CGI)
FROM php:8.2-apache

# Activer rewrite
RUN a2enmod rewrite

# Dépendances système
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

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dossier de travail
WORKDIR /var/www/html

# Copier le projet
COPY . .

# Créer dossiers Symfony + permissions
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var public

# Installer dépendances Symfony
RUN composer install --no-dev --optimize-autoloader

# Apache doit pointer vers /public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' \
    /etc/apache2/sites-available/000-default.conf

# IMPORTANT : forcer Apache + mod_php (PAS CGI)
RUN rm -f /etc/apache2/conf-enabled/php*.conf

EXPOSE 80

CMD ["apache2-foreground"]
