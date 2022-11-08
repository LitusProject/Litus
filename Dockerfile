FROM caddy:2.6.2 AS caddy

FROM composer:2.4.4 AS composer

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

FROM dunglas/frankenphp:latest-alpine AS frankenphp

ARG APPLICATION_ENV=development
ENV APPLICATION_ENV=${APPLICATION_ENV}
ARG COMMIT_SHA
ENV COMMIT_SHA=${COMMIT_SHA}

RUN install-php-extensions \
  imagick \
  intl \
  mailparse \
  opcache \
  pdo_pgsql \
  pgsql \
  redis \
  soap \
  zip

RUN apk add --no-cache \
    openjdk11-jre && \
  apk add --no-cache --repository http://dl-cdn.alpinelinux.org/alpine/v3.10/main --update-cache \
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

COPY --from=composer /app/ /app/

COPY docker/frankenphp/litus.ini /usr/local/etc/php/conf.d/
COPY docker/frankenphp/entrypoint.sh /

VOLUME ["/app/public/_assetic"]
VOLUME ["/app/public/_common/profile"]
VOLUME ["/app/public/_gallery/albums"]
VOLUME ["/app/public/_publications/pdf"]
VOLUME ["/app/public/_publications/html"]
VOLUME ["/app/public/_br/img"]

VOLUME ["/data"]

ENTRYPOINT ["/entrypoint.sh"]
