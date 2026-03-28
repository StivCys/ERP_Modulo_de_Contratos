#!/bin/sh

cd /var/www || exit 1

if [ ! -f artisan ]; then
  echo "Projeto Laravel não encontrado!"
  exit 1
fi

if [ ! -f .env ]; then
  echo "Criando .env..."
  cp .env.example .env
  php artisan key:generate
fi

echo "Aguardando banco de dados..."

until php artisan migrate:status > /dev/null 2>&1
do
  echo "Banco ainda não respondeu..."
  sleep 2
done

echo "Rodando migrations..."
php artisan migrate --force

echo "Rodando seeders..."
php artisan db:seed --force || true

echo "Matando processos antigos..."
kill -9 $(lsof -t -i:8000) 2>/dev/null || true

echo "Iniciando Octane..."
php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000