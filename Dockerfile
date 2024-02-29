# renovate: datasource=docker depName=php
ARG PHP_VERSION=8.3.2

# renovate: datasource=docker depName=alpine
ARG ALPINE_VERSION=3.18

FROM php:${PHP_VERSION}-alpine${ALPINE_VERSION}

# php extensions installer: https://github.com/mlocati/docker-php-extension-installer
COPY --from=mlocati/php-extension-installer --link /usr/bin/install-php-extensions /usr/local/bin/

RUN set -eux; \
	install-php-extensions pdo_mysql mongodb

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV PATH="${PATH}:/root/.composer/vendor/bin"
ENV COMPOSER_HOME=/tmp/composer
ENV COMPOSER_CACHE_DIR=/tmp/composer/cache
RUN mkdir -p /tmp/composer/cache
RUN chmod ugo+w /tmp/composer/cache

COPY --from=composer /usr/bin/composer /usr/bin/composer

CMD ["php"]
