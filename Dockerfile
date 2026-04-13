FROM php:8.4-cli

## Install system dependencies + libraries for GD & Imagick
RUN apt-get update && apt-get install -y \
    unzip git curl libzip-dev zip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libmagickwand-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy project
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist

RUN php artisan migrate --force || true

# Laravel setup
RUN php artisan config:clear || true
RUN php artisan cache:clear || true

# Expose port
EXPOSE 8000

# Start Laravel
CMD php artisan migrate:fresh --seed --force && php artisan serve --host=0.0.0.0 --port=8000