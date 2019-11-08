# PHP7-FPM
FROM php:7.1-fpm

MAINTAINER Vendor="Marius C." Description="PHP-FPM latest version"

LABEL version="1.0"

RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng12-dev \
        libmcrypt-dev \
        libicu-dev \
        libpq-dev \
        libbz2-dev \
        curl \
        git \
        unzip \
        wget \
    && docker-php-ext-install iconv \
    && docker-php-ext-install mcrypt \
    && docker-php-ext-install zip \
    && docker-php-ext-install bz2 \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install intl \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install opcache \
    && docker-php-ext-configure gd \
        --enable-gd-native-ttf \
        --with-freetype-dir=/usr/include/freetype2 \
        --with-png-dir=/usr/include \
        --with-jpeg-dir=/usr/include \
    && docker-php-ext-install gd \
    && docker-php-ext-install mbstring \
    && docker-php-ext-enable opcache gd

# install composer globally
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN ln -sf /dev/stdout /var/log/access.log && ln -sf /dev/stderr /var/log/error.log

COPY php.ini /usr/local/etc/php/

RUN /bin/bash -c 'rm -f /usr/local/etc/php-fpm.d/www.conf.default'
ADD www.pool.conf /usr/local/etc/php-fpm.d/
RUN rm -f /usr/local/etc/php-fpm.d/www.conf

RUN usermod -u 1000 www-data

CMD ["php-fpm"]

EXPOSE 9000
