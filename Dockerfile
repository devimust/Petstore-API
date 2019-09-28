
# Taken and modified from here
# @link https://github.com/Cyber-Duck/php-fpm-laravel/blob/7.1/Dockerfile
FROM php:7.1-fpm

ENV XDEBUG="false"

RUN apt-get update && \
    apt-get install -y --force-yes --no-install-recommends \
        libmemcached-dev \
        libz-dev \
        libpq-dev \
        libjpeg-dev \
        libpng-dev \
        libfreetype6-dev \
        libssl-dev \
        libmcrypt-dev \
        openssh-server \
        libmagickwand-dev \
        git \
        cron \
        nano \
        libxml2-dev

# Install soap extention
RUN docker-php-ext-install soap

# Install for image manipulation
RUN docker-php-ext-install exif

# Install the PHP mcrypt extention
RUN docker-php-ext-install mcrypt

# Install the PHP pcntl extention
RUN docker-php-ext-install pcntl

# Install the PHP zip extention
RUN docker-php-ext-install zip

# Install the PHP pdo_mysql extention
RUN docker-php-ext-install pdo_mysql

# Install the PHP pdo_pgsql extention
RUN docker-php-ext-install pdo_pgsql

# Install the PHP bcmath extension
RUN docker-php-ext-install bcmath

#####################################
# Imagick:
#####################################

RUN pecl install imagick && \
    docker-php-ext-enable imagick

#####################################
# GD:
#####################################

# Install the PHP gd library
RUN docker-php-ext-install gd && \
    docker-php-ext-configure gd \
        --enable-gd-native-ttf \
        --with-jpeg-dir=/usr/lib \
        --with-freetype-dir=/usr/include/freetype2 && \
    docker-php-ext-install gd

#####################################
# xDebug:
#####################################

# Install the xdebug extension
RUN pecl install xdebug && docker-php-ext-enable xdebug
# Copy xdebug configration for remote debugging
COPY ./docker/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

#####################################
# PHP Memcached:
#####################################

# Install the php memcached extension
RUN pecl install memcached && docker-php-ext-enable memcached

#
#--------------------------------------------------------------------------
# Final setup
#--------------------------------------------------------------------------
#
ADD ./docker/php.ini /usr/local/etc/php/conf.d
RUN rm -r /var/lib/apt/lists/*
RUN usermod -u 1000 www-data
COPY . /var/www

#
#--------------------------------------------------------------------------
# Environment
#--------------------------------------------------------------------------
#
WORKDIR /var/www
EXPOSE 9000
CMD php artisan serve --host=0.0.0.0 --port=8000
