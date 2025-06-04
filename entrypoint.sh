#!/bin/bash

php artisan config:cache

# Espera o banco ficar pronto
until mysqladmin ping -h"${DB_HOST}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent; do
  echo "⌛ Aguardando banco...";
  sleep 3;
done

php artisan migrate --force
php artisan db:seed --force

exec apache2-foreground
