# Utilise une image officielle PHP avec Apache
FROM php:8.2-apache

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev zip unzip libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copier les fichiers Laravel dans le container
COPY . /var/www/html

# Installer les dépendances Laravel
WORKDIR /var/www/html
RUN composer install --no-dev --optimize-autoloader

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copier le fichier .env (facultatif si tu passes les variables via Render)
# COPY .env.example .env

# Générer le cache de config, routes et vues
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Lancer les migrations automatiquement (avec --force)
CMD php artisan migrate --force && apache2-foreground
