# Stage 0 : Base PHP
FROM php:8.4-fpm AS base

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    zlib1g-dev \
    libicu-dev \
    libxml2-dev \
    && docker-php-ext-install intl pdo_mysql mbstring zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le projet
COPY . .

# Créer les dossiers cache/log et attribuer les permissions
RUN mkdir -p var/cache var/log \
    && chown -R www-data:www-data var public

# Autoriser symfony/flex et symfony/runtime pour composer
RUN composer config --no-plugins allow-plugins.symfony/flex true
RUN composer config --no-plugins allow-plugins.symfony/runtime true

# Installer les dépendances PHP sans dev, optimisé pour production
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --optimize-autoloader

# Exposer le port PHP-FPM
EXPOSE 9000

# Commande par défaut pour PHP-FPM
CMD ["php-fpm"]
