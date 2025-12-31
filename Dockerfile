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
# Installer les dépendances système
# ------------------------------
RUN apt-get update && apt-get install -y \
    git unzip zip libicu-dev libonig-dev libxml2-dev libzip-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl pdo_mysql mbstring xml zip gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ------------------------------
# Installer Composer
# ------------------------------
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ------------------------------
# Définir le répertoire de travail
# ------------------------------
WORKDIR /var/www/html

# ------------------------------
# Copier uniquement les fichiers Composer pour optimiser le cache
# ------------------------------
COPY composer.json composer.lock ./

# Installer les dépendances PHP (prod)
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --no-scripts

# ------------------------------
# Copier le reste du projet
# ------------------------------
COPY . .

# ------------------------------
# Configuration de l'environnement
# ------------------------------
ENV APP_ENV=prod
ENV APP_DEBUG=0

# ------------------------------
# Droits et Cache Symfony
# ------------------------------
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var public vendor config

# ------------------------------
# Exposer le port HTTP
# ------------------------------
EXPOSE 80

# Apache démarre automatiquement via l'image de base
