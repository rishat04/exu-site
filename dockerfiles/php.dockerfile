FROM php:8-fpm-alpine

ARG UID
ARG GID

ENV UID=${UID}
ENV GID=${GID}

RUN mkdir -p /var/www/html

WORKDIR /var/www/html

# MacOS staff group's gid is 20, so is the dialout group in alpine linux. We're not using it, let's just remove it.
RUN delgroup dialout

RUN addgroup -g ${GID} --system laravel
RUN adduser -G laravel --system -D -s /bin/sh -u ${UID} laravel

RUN sed -i "s/user = www-data/user = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN sed -i "s/group = www-data/group = laravel/g" /usr/local/etc/php-fpm.d/www.conf
RUN echo "php_admin_flag[log_errors] = on" >> /usr/local/etc/php-fpm.d/www.conf
RUN apk update
RUN apk add --upgrade linux-headers
RUN apk add --no-cache  mysql-client msmtp perl wget procps shadow libzip libpng libjpeg-turbo libwebp freetype icu imagemagick
RUN apk add --no-cache --virtual build-essentials \
    postgresql-dev icu-dev icu-libs zlib-dev g++ make automake autoconf libzip-dev libpq-dev imagemagick-dev \
    libpng-dev libwebp-dev libjpeg-turbo-dev freetype-dev && \
    docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install -j$(nproc) gd pdo pdo_mysql pdo_pgsql zip exif pcntl
RUN docker-php-ext-enable pdo pdo_mysql pdo_pgsql gd zip exif pcntl
RUN apk add --no-cache \
    $PHPIZE_DEPS \
    && pecl install xdebug imagick \
    && docker-php-ext-enable xdebug imagick
RUN { \
            echo "xdebug.mode=develop,coverage,debug,profile"; \
            echo "xdebug.client_host=host.docker.internal"; \
            echo "xdebug.client_port=9003"; \
            echo "zend_extension=xdebug.so"; \
            echo "xdebug.start_with_request=yes"; \
            echo "xdebug.connect_timeout_ms=2000"; \
    } > /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini;
RUN mkdir -p /usr/src/php/ext/redis \
    && curl -L https://github.com/phpredis/phpredis/archive/5.3.4.tar.gz | tar xvz -C /usr/src/php/ext/redis --strip 1 \
    && echo 'redis' >> /usr/src/php-available-exts \
    && docker-php-ext-install redis


CMD ["php-fpm", "-y", "/usr/local/etc/php-fpm.conf", "-R"]
