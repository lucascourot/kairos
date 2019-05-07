FROM wakeonweb/php:7.2-apache-dev

ARG COMPOSER_AUTH

ENV APP_ENV=dev \
    APP_DEBUG=0 \
    APP_LOCALE=fr

USER root
RUN apt-get update || apt-get update

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql  && docker-php-ext-install pdo pdo_pgsql pgsql zip && docker-php-ext-enable amqp

WORKDIR /var/www/project

