FROM php:8.1

# Installation de symfony-cli
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash

RUN apt-get update -y && apt-get install -y libmcrypt-dev libonig-dev libzip-dev symfony-cli

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo pdo_mysql mbstring zip

WORKDIR /app
COPY . /app

RUN composer install

EXPOSE 8000
# CMD php bin/console server:run 0.0.0.0:8000
# CMD curl -sS https://get.symfony.com/cli/installer | bash && /tmp/symfony server:start
CMD symfony server:start