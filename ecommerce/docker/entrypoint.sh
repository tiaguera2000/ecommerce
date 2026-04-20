#!/bin/sh
set -e

# Cria .env a partir do .env.example se não existir
if [ ! -f /var/www/.env ]; then
  cp /var/www/.env.example /var/www/.env
fi

# Gera APP_KEY se não existir
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

# Roda migrations e seed
php artisan migrate --force
php artisan db:seed --force

# Limpa e otimiza caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php-fpm
