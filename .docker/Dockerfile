# FROM php:7.2-apache

# MAINTAINER Muhammad Ehsanul Hoque


# RUN apt-get update && apt-get update && apt-get install -y libpng-dev libjpeg-dev libcurl4-openssl-dev && rm -rf /var/lib/apt/lists/* \
# 	&& docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
# 	&& docker-php-ext-install gd mysqli opcache

# # @link https://github.com/docker-library/php/issues/331#issuecomment-261016528   Really thanks
# RUN docker-php-ext-install curl mysqli opcache pdo pdo_mysql zip

# # @link https://hub.docker.com/r/plab/docker-php/~/dockerfile/
# RUN apt-get update && apt-get install -y \
# 	bzip2 \
# 	libbz2-dev \
# 	libfreetype6-dev \
# 	libjpeg62-turbo-dev \
# 	libmcrypt-dev \
# 	libpng-dev \
# 	libghc-postgresql-libpq-dev \
# 	&& docker-php-ext-install mbstring bz2 zip \
# 	&& docker-php-ext-configure gd -with-freetype-dir=/usr/include/ -with-jpeg-dir=/usr/include/ \
# 	&& docker-php-ext-configure pgsql -with-pgsql=/usr/include/postgresql/ \
# 	&& docker-php-ext-install gd pgsql pdo_pgsql

# RUN apt-get update \
#   && apt-get install -y libmemcached11 libmemcachedutil2 build-essential libmemcached-dev libz-dev \
#   && pecl install memcached \
#   && echo extension=memcached.so >> /usr/local/etc/php/conf.d/memcached.ini \
#   && apt-get remove -y build-essential libmemcached-dev libz-dev \
#   && apt-get autoremove -y \
#   && apt-get clean \
#   && rm -rf /tmp/pear

# # @link https://github.com/docker-library/php/issues/57
# RUN apt-get update \
#   && apt-get install -y zlib1g-dev libicu-dev g++ \
#   && docker-php-ext-configure intl \
#   && docker-php-ext-install intl

# # set recommended PHP.ini settings
# # see https://secure.php.net/manual/en/opcache.installation.php
# RUN { \
# 		echo 'opcache.memory_consumption=128'; \
# 		echo 'opcache.interned_strings_buffer=8'; \
# 		echo 'opcache.max_accelerated_files=4000'; \
# 		echo 'opcache.revalidate_freq=2'; \
# 		echo 'opcache.fast_shutdown=1'; \
# 		echo 'opcache.enable_cli=1'; \
# 	} > /usr/local/etc/php/conf.d/opcache-recommended.ini

# RUN a2enmod rewrite expires vhost_alias

# RUN apt install -y ca-certificates apt-transport-https
# RUN apt-get install -y wget gnupg2
# RUN apt-get update
# RUN wget -q https://packages.sury.org/php/apt.gpg -O- | apt-key add -
# RUN echo "deb https://packages.sury.org/php/ stretch main" | tee /etc/apt/sources.list.d/php.list


# RUN apt-get install -y mariadb-client libxml2-dev

# RUN docker-php-ext-install xml

# RUN apt-get install -y openssl libssl-dev

# COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# VOLUME /var/www

# RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# RUN chown -R www-data:www-data /var/www \
#     && a2enmod rewrite

# RUN pecl install -o -f redis && rm -rf /tmp/pear && docker-php-ext-enable redis




FROM php:7.2-apache


# Fix old Debian repo sources
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list \
    && sed -i '/security.debian.org/d' /etc/apt/sources.list \
    && apt-get update

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libcurl4-openssl-dev \
    bzip2 libbz2-dev libfreetype6-dev \
    libjpeg62-turbo-dev libmcrypt-dev libpng-dev \
    libghc-postgresql-libpq-dev \
    libmemcached11 libmemcachedutil2 build-essential \
    libmemcached-dev libz-dev zlib1g-dev libicu-dev g++ \
    mariadb-client libxml2-dev wget gnupg2 curl unzip git \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-png-dir=/usr --with-jpeg-dir=/usr \
    --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install gd mysqli opcache \
    && docker-php-ext-install curl pdo pdo_mysql zip \
    && docker-php-ext-install mbstring bz2 intl xml

# Install redis extension
RUN pecl install redis-5.3.7 \
    && docker-php-ext-enable redis

# Enable Apache mods
RUN a2enmod rewrite expires vhost_alias

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Disable broken sury PHP repo (not needed anymore)
RUN rm -f /etc/apt/sources.list.d/php.list

# Install Node.js + npm
RUN curl -fsSL https://nodejs.org/dist/v14.21.3/node-v14.21.3-linux-x64.tar.xz -o node.tar.xz \
    && mkdir -p /usr/local/lib/nodejs \
    && tar -xJf node.tar.xz -C /usr/local/lib/nodejs \
    && rm node.tar.xz \
    && ln -s /usr/local/lib/nodejs/node-v14.21.3-linux-x64/bin/node /usr/local/bin/node \
    && ln -s /usr/local/lib/nodejs/node-v14.21.3-linux-x64/bin/npm /usr/local/bin/npm \
    && ln -s /usr/local/lib/nodejs/node-v14.21.3-linux-x64/bin/npx /usr/local/bin/npx

# Set working directory
WORKDIR /var/www/html

# Copy Apache vhost
COPY .docker/vhost.conf /etc/apache2/sites-available/000-default.conf

# Copy project files into container
COPY . .



# ✅ Fix permissions (AFTER files are copied, and using correct path)
RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80
