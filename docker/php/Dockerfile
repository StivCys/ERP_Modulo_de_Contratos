FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    netcat-openbsd \
    lsof \
    git curl unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libssl-dev \
    zlib1g-dev \
    libcurl4-openssl-dev \
    pkg-config \
    build-essential \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN pecl install swoole redis \
    && docker-php-ext-enable swoole redis

WORKDIR /var/www

ENV HOME=/var/www

# Copia apenas composer primeiro (melhora cache)
COPY src/composer.json src/composer.lock* ./

RUN composer install --no-interaction --prefer-dist || true

# Depois copia o resto
COPY src/ .

COPY docker/php/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 8000

ENTRYPOINT ["/start.sh"]