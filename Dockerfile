# ------------------------------
# Image de base : PHP 8.4 avec Apache
# ------------------------------
FROM php:8.4-apache

# ------------------------------
# Configuration d'Apache pour Symfony
# ------------------------------
# On change le DocumentRoot vers le dossier /public de Symfony
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Activer le module Rewrite d'Apache (nécessaire pour Symfony)
RUN a2enmod rewrite

# ------------------------------
# Installer les dépendances système
# ------------------------------
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
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
# Copier le projet
# ------------------------------
# On copie tout le projet dans le conteneur
COPY . .

# ------------------------------
# Configuration de l'environnement
# ------------------------------
ENV APP_ENV=prod
ENV APP_DEBUG=0

# ------------------------------
# Installer les dépendances PHP
# ------------------------------
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --no-scripts

# ------------------------------
# Droits et Cache Symfony
# ------------------------------
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var public \
    && php bin/console cache:clear --no-warmup \
    && php bin/console cache:warmup

# ------------------------------
# Exposer le port HTTP (Render détectera ce port)
# ------------------------------
EXPOSE 80

# Apache démarre automatiquement via l'image de base
