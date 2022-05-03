FROM php:8.0-apache

# Install dependencies
RUN set -ex; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        libbz2-dev \
        libfreetype6-dev \
        libjpeg-dev \
        libpng-dev \
        libwebp-dev \
        libxpm-dev \
        libzip-dev \
    ; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-xpm; \
    docker-php-ext-install -j "$(nproc)" \
        bz2 \
        gd \
        mysqli \
        opcache \
        zip \
    ; \
# set recommended PHP.ini settings
# see https://secure.php.net/manual/en/opcache.installation.php
ENV MAX_EXECUTION_TIME 600
ENV MEMORY_LIMIT 512M
ENV UPLOAD_LIMIT 2048K
RUN set -ex; \
    \
    { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
    } > $PHP_INI_DIR/conf.d/opcache-recommended.ini; \
    \
    { \
        echo 'session.cookie_httponly=1'; \
        echo 'session.use_strict_mode=1'; \
    } > $PHP_INI_DIR/conf.d/session-strict.ini; \
    \
    { \
        echo 'allow_url_fopen=Off'; \
        echo 'max_execution_time=${MAX_EXECUTION_TIME}'; \
        echo 'max_input_vars=10000'; \
        echo 'memory_limit=${MEMORY_LIMIT}'; \
        echo 'post_max_size=${UPLOAD_LIMIT}'; \
        echo 'upload_max_filesize=${UPLOAD_LIMIT}'; \
    } > $PHP_INI_DIR/conf.d/phpmyadmin-misc.ini

# Calculate download URL
ENV VERSION 5.1.3
ENV SHA256 c562feddc0f8ff5e69629113f273a0d024a65fb928c48e89ce614744d478296f
ENV URL https://github.com/nyxtechnology/automessage/archive/refs/heads/stage.zip

LABEL org.opencontainers.image.title="Official phpMyAdmin Docker image" \
    org.opencontainers.image.description="Run phpMyAdmin with Alpine, Apache and PHP FPM." \
    org.opencontainers.image.authors="The phpMyAdmin Team <developers@phpmyadmin.net>" \
    org.opencontainers.image.vendor="phpMyAdmin" \
    org.opencontainers.image.documentation="https://github.com/phpmyadmin/docker#readme" \
    org.opencontainers.image.licenses="GPL-2.0-only" \
    org.opencontainers.image.version="${VERSION}" \
    org.opencontainers.image.url="https://github.com/phpmyadmin/docker#readme" \
    org.opencontainers.image.source="https://github.com/phpmyadmin/docker.git"

# Download automessage zip, verify it using gpg and extract
RUN set -ex; \
    apt-get update; \
    apt-get install -y --no-install-recommends \
        gnupg \
        dirmngr \
    ; \
    \
    curl -fsSL -o automessage-stage.zip $URL; \
    curl -fsSL -o automessage-stage.zip.asc $URL.asc; \
    gpg --batch --verify phpMyAdmin.tar.xz.asc automessage-stage.zip; \
    zip -xf phpMyAdmin.tar.xz -C /var/www/html --strip-components=1; \
    mkdir -p /var/www/html/tmp; \
    chown www-data:www-data /var/www/html/tmp; \
    gpgconf --kill all; \
    rm -r "$GNUPGHOME" automessage-stage.zip automessage-stage.zip.asc; \
    rm -r -v /var/www/html/setup/ /var/www/html/examples/ /var/www/html/js/src/ /var/www/html/templates/test/ /var/www/html/babel.config.json /var/www/html/doc/html/_sources/ /var/www/html/RELEASE-DATE-$VERSION /var/www/html/CONTRIBUTING.md; \
    sed -i "s@define('CONFIG_DIR'.*@define('CONFIG_DIR', '/etc/automessage/');@" /var/www/html/libraries/vendor_config.php; \
    \
    apt-mark auto '.*' > /dev/null; \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false; \
    rm -rf /var/lib/apt/lists/

CMD ["apache2-foreground"]