FROM php:8.3-fpm-alpine

# Установка зависимостей (apk вместо apt-get)
RUN apk add --no-cache --update \
    build-base \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    libxml2-dev \
    postgresql-dev \
    jpegoptim \
    optipng \
    pngquant \
    gifsicle \
    vim \
    unzip \
    git \
    curl \
    npm \
    # Для gd:
    freetype \
    libjpeg-turbo \
    # Для composer:
    openssl \
    # Для Redis:
    autoconf \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip exif pcntl bcmath \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && pecl install redis \
    && docker-php-ext-enable redis opcache

# Установка Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Создаем необходимые папки
RUN mkdir -p /var/www/storage /var/www/bootstrap/cache

# Настройка прав
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Установка Node.js (в Alpine npm идет вместе с node)
RUN npm install -g npm@latest

# Создаем PHP конфигурацию для оптимизации
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=128" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=8" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=4000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=2" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Настройка PHP для производительности
RUN echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "max_input_vars = 3000" >> /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www

# Копируем только composer.json сначала
COPY composer.json .
RUN composer install --no-scripts --no-autoloader --no-dev

# Копируем остальные файлы
COPY . .

# Генерация autoload и оптимизация
RUN composer dump-autoload --optimize --no-scripts \
    && chown -R www-data:www-data /var/www
