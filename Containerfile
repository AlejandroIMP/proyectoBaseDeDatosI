FROM docker.io/library/composer:2 AS composer

FROM docker.io/library/php:8.4-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        zip \
        curl \
        gnupg2 \
        libpng-dev \
        libonig-dev \
        libxml2-dev \
        libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# install-php-extensions (mlocati) resuelve automaticamente la instalacion
# del driver ODBC de Microsoft (msodbcsql18) necesario para sqlsrv/pdo_sqlsrv.
ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
        pdo_sqlsrv \
        sqlsrv \
        pdo_mysql \
        gd \
        intl \
        zip \
        bcmath \
        pcntl \
        opcache

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
