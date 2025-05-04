FROM php:8.0-fpm

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev libzip-dev git unzip && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd zip pdo pdo_mysql

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier les fichiers du projet dans l'image
COPY . /var/www

# Définir les permissions
RUN chown -R www-data:www-data /var/www

# Installer les dépendances via Composer
WORKDIR /var/www
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Configurer le serveur pour servir Laravel
CMD ["php", "-S", "0.0.0.0:3000", "-t", "public"]
