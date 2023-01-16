FROM php:8.1.5-fpm-buster

ARG HOST_GID=1000
ARG HOST_UID=1000
ARG XDEBUG_CLIENT_PORT=9001
ARG XDEBUG_CLIENT_HOST=host.docker.internal
ARG WITH_XDEBUG=false

# ADD user and set group
RUN addgroup --gid ${HOST_GID} symfony
RUN adduser --gid ${HOST_GID} --uid ${HOST_UID} --shell /bin/sh --disabled-password --disabled-login --gecos "" symfony

RUN set -eux; \
  apt-get update -y --allow-unauthenticated --allow-insecure-repositories

# install symfony deps
RUN apt-get install -y --no-install-recommends \
        libzip-dev \
        zip \
        libpq-dev \
        libicu-dev

# confiogure needed php extensions
RUN docker-php-ext-configure intl

# install needed php extensions
RUN docker-php-ext-install \
  zip \
  pdo pdo_pgsql \
  intl

# Add xdebug extension for develop purpose
RUN if [ $WITH_XDEBUG = "true" ] ; then \
  pecl install xdebug; \
  docker-php-ext-enable xdebug; \
  echo "xdebug.mode=debug" >> "${PHP_INI_DIR}"/conf.d/docker-php-ext-xdebug.ini; \
  echo "xdebug.client_port=${XDEBUG_CLIENT_PORT}" >> "${PHP_INI_DIR}"/conf.d/docker-php-ext-xdebug.ini; \
  echo "xdebug.client_host=${XDEBUG_CLIENT_HOST}" >> "${PHP_INI_DIR}"/conf.d/docker-php-ext-xdebug.ini; \
  echo "xdebug.start_with_request=yes" >> "${PHP_INI_DIR}"/conf.d/docker-php-ext-xdebug.ini; \
fi ;

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Clean up apt cache 
RUN rm -rf /var/lib/apt/lists/*

# Create folder to run
RUN mkdir -p /spalopia
# Set Profile
RUN chown symfony:symfony /spalopia
# Copy sources
COPY ./spalopia /spalopia
# Work in the specific space
WORKDIR /spalopia

RUN chown ${HOST_UID}:${HOST_GID} /spalopia -R

COPY ./docker/php/entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

ENTRYPOINT [ "entrypoint" ]
CMD ["php-fpm"]
