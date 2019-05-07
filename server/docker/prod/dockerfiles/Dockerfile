FROM wakeonweb/php:7.2-apache

ARG COMPOSER_AUTH

ENV APP_ENV=prod \
    APP_DEBUG=0 \
    APP_LOCALE=fr

USER root
RUN apt-get update || apt-get update

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql  && docker-php-ext-install pdo pdo_pgsql pgsql && docker-php-ext-enable amqp

COPY ./ /var/www/project/

WORKDIR /var/www/project

RUN composer global require hirak/prestissimo --ansi \
    && composer install --optimize-autoloader --no-dev --no-progress --no-suggest --classmap-authoritative --no-interaction

ENTRYPOINT ["docker-secret-entrypoint"]

RUN echo "umask 002" >> /etc/apache2/envvars
RUN /etc/init.d/apache2 restart

RUN usermod -a -G www-data dockeruser

CMD ["apache2-foreground"]
