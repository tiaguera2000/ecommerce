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

# Roda migrations (idempotente, seguro rodar sempre)
php artisan migrate --force

# Roda seed apenas se o banco estiver vazio (primeira vez)
USER_COUNT=$(php artisan tinker --no-interaction --execute="echo \App\Models\User::count();" 2>/dev/null | tr -d '[:space:]')
if [ "$USER_COUNT" = "0" ]; then
  php artisan db:seed --force
fi

# Limpa e otimiza caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php-fpm
