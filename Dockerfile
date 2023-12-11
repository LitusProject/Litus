# dependencies
FROM caddy:2.7.6 AS caddy

# development
FROM composer:2.6.6 AS composer

ARG APPLICATION_ENV=development
ENV APPLICATION_ENV=${APPLICATION_ENV}
ARG COMMIT_SHA
ENV COMMIT_SHA=${COMMIT_SHA}

COPY composer.* /app/

RUN \
  if [ "${APPLICATION_ENV}" = "development" ]; then \
    composer install \
      --ignore-platform-reqs \
      --no-interaction \
      --no-progress \
      --no-scripts \
      --no-suggest \
      --optimize-autoloader \
      --prefer-dist; \
  else \
    composer install \
      --ignore-platform-reqs \
      --no-dev \
      --no-interaction \
      --no-progress \
      --no-scripts \
      --no-suggest \
      --optimize-autoloader \
      --prefer-dist; \
  fi

COPY . /app/

RUN \
  if [ "${APPLICATION_ENV}" = "development" ]; then \
    composer dump-autoload \
      --classmap-authoritative \
      --optimize; \
  else \
    composer dump-autoload \
      --classmap-authoritative \
      --no-dev \
      --optimize; \
  fi

FROM php:8.3.0-cli-alpine AS php-cli

ARG APPLICATION_ENV=development
ENV APPLICATION_ENV=${APPLICATION_ENV}
ARG COMMIT_SHA
ENV COMMIT_SHA=${COMMIT_SHA}

RUN apk add --no-cache \
    icu \
    imagemagick \
    libgomp \
    libpq \
    libxml2 \
    libzip && \
  apk add --no-cache --virtual .phpize-deps \
    $PHPIZE_DEPS \
    icu-dev \
    imagemagick-dev \
    libxml2-dev \
    libzip-dev \
    postgresql-dev && \
  docker-php-ext-install "-j$(nproc)" \
    intl \
    opcache \
    pdo_pgsql \
    pgsql \
    soap \
    zip && \
  pecl install imagick && \
  docker-php-ext-enable imagick && \
  pecl install mailparse && \
  docker-php-ext-enable mailparse && \
  pecl install redis && \
  docker-php-ext-enable redis && \
  apk del .phpize-deps

RUN apk add --no-cache \
    openjdk11-jre && \
  apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/v3.10/main --update-cache \
    nodejs==10.24.1-r0 \
    npm==10.24.1-r0 && \
  npm install -g less

RUN mv "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini"

COPY --from=composer /app/ /app/

COPY docker/php-cli/litus.ini /usr/local/etc/php/conf.d/
COPY docker/php-cli/entrypoint.sh /

ENTRYPOINT ["/entrypoint.sh"]

FROM php:8.3.0-fpm-alpine AS php-fpm

ARG APPLICATION_ENV=development
ENV APPLICATION_ENV=${APPLICATION_ENV}
ARG COMMIT_SHA
ENV COMMIT_SHA=${COMMIT_SHA}

RUN apk add --no-cache \
  icu \
  imagemagick \
  libgomp \
  libpq \
  libxml2 \
  libzip \
  openjdk11-jre

RUN apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/v3.10/main --update-cache \
    nodejs==10.24.1-r0 \
    npm==10.24.1-r0 && \
  npm install -g less

RUN curl -fsSL -o /tmp/fop-2.7-bin.tar.gz https://downloads.apache.org/xmlgraphics/fop/binaries/fop-2.7-bin.tar.gz && \
  tar --strip-components=1 -C /opt -xzf /tmp/fop-2.7-bin.tar.gz fop-2.7/fop && \
  rm /tmp/fop-2.7-bin.tar.gz

RUN mv "${PHP_INI_DIR}/php.ini-production" "${PHP_INI_DIR}/php.ini"

RUN mkdir -p /app/public/_assetic && \
  mkdir -p /app/public/_common/profile && \
  mkdir -p /app/public/_gallery/albums && \
  mkdir -p /app/public/_publications/pdf && \
  mkdir -p /app/public/_publications/html && \
  mkdir -p /app/public/_br/img && \
  chown -R www-data:www-data /app

COPY --from=php-cli --chown=www-data:www-data /app/ /app/

COPY --from=php-cli /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=php-cli /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/

COPY docker/php-fpm/litus.ini /usr/local/etc/php/conf.d/
COPY docker/php-fpm/opcache.ini /usr/local/etc/php/conf.d/

VOLUME ["/app/public/_assetic"]
VOLUME ["/app/public/_common/profile"]
VOLUME ["/app/public/_gallery/albums"]
VOLUME ["/app/public/_publications/pdf"]
VOLUME ["/app/public/_publications/html"]
VOLUME ["/app/public/_br/img"]

VOLUME ["/data"]

FROM scratch

COPY --from=caddy /etc/ssl/certs/ca-certificates.crt /etc/ssl/certs/
COPY --from=caddy /usr/bin/caddy /usr/bin/

COPY --from=composer /app/public/ /app/public/

COPY docker/caddy/Caddyfile /etc/caddy/

VOLUME ["/app/public/_assetic"]
VOLUME ["/app/public/_common/profile"]
VOLUME ["/app/public/_gallery/albums"]
VOLUME ["/app/public/_publications/pdf"]
VOLUME ["/app/public/_publications/html"]
VOLUME ["/app/public/_br/img"]

EXPOSE 8080

ENTRYPOINT ["caddy"]
CMD ["run", "--config", "/etc/caddy/Caddyfile", "--adapter", "caddyfile"]
