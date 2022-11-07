FROM php:8.1

RUN apt-get update -y && apt-get install -y libmcrypt-dev libonig-dev libzip-dev
# RUN curl -sS https://get.symfony.com/cli/installer | bash

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo mbstring zip

WORKDIR /app
COPY . /app

# RUN composer install
# RUN symfony --version
# RUN /bin/sh -c symfony --version

EXPOSE 8000
# CMD php bin/console server:run 0.0.0.0:8000
CMD curl -sS https://get.symfony.com/cli/installer | bash && /tmp/symfony server:start