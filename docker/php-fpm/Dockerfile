FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libicu-dev \
    libzip-dev \
    librabbitmq-dev \
    pkg-config \
    libssl-dev \
    libzstd-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    intl \
    zip \
    bcmath \
    pcntl \
    opcache \
    sockets

RUN pecl install mongodb amqp xdebug \
    && docker-php-ext-enable mongodb amqp xdebug

RUN echo "xdebug.mode=coverage" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

COPY --from=composer:2.8.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/project

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

COPY --chown=www-data:www-data . .

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
