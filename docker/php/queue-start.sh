#!/bin/sh

echo "Aguardando Redis..."

until nc -z redis 6379; do
  echo "Redis não está pronto..."
  sleep 1
done

echo "Redis pronto!"

echo "Aguardando banco..."

until nc -z db 3306; do
  echo "Banco não está pronto..."
  sleep 1
done

echo "Banco pronto!"

echo "Iniciando queue worker..."

php artisan queue:work --tries=3 --timeout=90