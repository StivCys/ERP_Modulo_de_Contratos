#!/bin/sh

cd /var/www || exit 1

if [ ! -f artisan ]; then
  echo "Projeto Laravel não encontrado!"
  exit 1
fi

if [ ! -d "vendor" ]; then
  echo "Instalando dependências..."
  composer install --no-interaction --prefer-dist
fi

if [ ! -f .env ]; then
  echo "Criando .env..."
  cp .env.example .env
  php artisan key:generate
fi

if ! grep -q "APP_KEY=base64" .env; then
  echo "Gerando APP_KEY..."
  php artisan key:generate
fi

echo "Aguardando banco de dados..."

until php -r "
try {
    new PDO('mysql:host=db;port=3306;dbname=laravel', 'laravel', 'laravel');
} catch (Exception \$e) {
    exit(1);
}
"; do
  echo "Banco ainda não respondeu..."
  sleep 2
done

echo "Banco pronto!"

echo "Rodando migrations..."
php artisan migrate --force

echo "Rodando seeders..."
php artisan db:seed --force || true

echo "Parando Octane (se estiver rodando)..."
php artisan octane:stop 2>/dev/null || true

echo "Iniciando Octane..."
exec php artisan octane:start --server=swoole --host=0.0.0.0 --port=8000
