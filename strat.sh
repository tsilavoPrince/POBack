#!/bin/bash

# Assurer que les permissions sont bonnes
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exécuter les commandes artisan pour nettoyer le cache et les configurations
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Exécuter les migrations Laravel (en utilisant le flag --force)
php artisan migrate --force

# Lancer le serveur Laravel en utilisant la variable d'environnement PORT fournie par Railway
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
