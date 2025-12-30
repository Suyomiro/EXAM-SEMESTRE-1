# ------------------------------
# Image de base PHP-FPM
# ------------------------------
FROM php:8.3-fpm

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
COPY . .

# ------------------------------
# Droits sur les dossiers de cache et logs
# ------------------------------
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var public

# ------------------------------
# Configuration de l'environnement
# ------------------------------
ENV APP_ENV=prod
ENV APP_DEBUG=0

# ------------------------------
# Installer les dépendances PHP
# (Utilise uniquement le composer.json copié)
# ------------------------------
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --no-scripts

# ------------------------------
# Préparer le cache Symfony
# ------------------------------
RUN php bin/console cache:clear --no-warmup \
    && php bin/console cache:warmup

# ------------------------------
# Exposer le port FPM
# ------------------------------
EXPOSE 9000

# ------------------------------
# Commande par défaut
# ------------------------------
CMD ["php-fpm"]
