# ------------------------------
# Image de base : PHP 8.4 avec Apache
# ------------------------------
FROM php:8.4-apache

# ------------------------------
# Configuration d'Apache pour Symfony
# ------------------------------
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf \
    && sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
    && a2enmod rewrite

# ------------------------------
# Dépendances système (PostgreSQL pour Neon + GD pour les images)
# ------------------------------
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libonig-dev libxml2-dev libzip-dev \
    libpng-dev libjpeg-dev libfreetype6-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo_pgsql mbstring xml zip gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ------------------------------
# Installation de Composer
# ------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ------------------------------
# Préparation du projet
# ------------------------------
WORKDIR /var/www/html
COPY composer.json composer.lock ./

# Installation propre (ignore les limites de version locale)
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-req=php

# Copie du reste du code
COPY . .

# ------------------------------
# Configuration Symfony & Permissions
# ------------------------------
ENV APP_ENV=prod
ENV APP_DEBUG=0

RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var public vendor config \
    && chmod -R 775 var/cache var/log

# 1. Générer le cache
# 2. On essaie de lancer les migrations automatiquement (si DATABASE_URL est prête)
RUN php bin/console cache:clear --env=prod

# ------------------------------
EXPOSE 80
