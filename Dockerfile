FROM php:8.1-apache

# Installation de symfony-cli
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash

# Installation des dépendances d'environnement
RUN apt-get update -y && apt-get install -y \
    libmcrypt-dev \
    libonig-dev \
    libzip-dev \
    libicu-dev \
    libpng-dev \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    cron \
    curl \
    nano \
    unzip \
    wget \
    git-core

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installation des extensions PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    zip \
    intl

# Sélection du dossier de travail
WORKDIR /app
COPY . /app
RUN chown -R www-data:www-data /app

# Installation des dépendances de Composer
RUN composer install

# Création d'un fichier de "démarrage" du serveur de test
COPY ./docker/entrypoint.sh /usr/local/bin/
RUN chmod 777 /usr/local/bin/entrypoint.sh \
    && ln -s /usr/local/bin/entrypoint.sh /

# Configuration de Apache
COPY ./docker/000-default.conf /etc/apache2/sites-available/000-default.conf
RUN sed -i "s/Listen 80/Listen ${PORT:-8080}/g" /etc/apache2/ports.conf
RUN a2enmod rewrite
RUN a2enmod proxy
RUN a2enmod proxy_http
RUN a2enmod proxy_balancer
RUN a2enmod lbmethod_byrequests
RUN a2enmod negotiation

# Configuration de PHP
COPY ./docker/php.ini /usr/local/etc/php/php.ini

USER www-data:www-data
RUN rm .env
RUN touch .env
RUN composer dump-env .env
EXPOSE 8080

# Démarrage du serveur de test
ENTRYPOINT ["/entrypoint.sh"]
# CMD ["symfony","server:start"]