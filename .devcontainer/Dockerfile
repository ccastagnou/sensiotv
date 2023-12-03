FROM php:8.2-fpm-bullseye

RUN apt-get update -y && \
    apt-get install -y libzip-dev libicu-dev libxslt1-dev && \
    docker-php-ext-install \
        zip \
        intl \
        xsl

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN set -uex; \
    apt-get update; \
    apt-get install -y ca-certificates curl gnupg; \
    mkdir -p /etc/apt/keyrings; \
    curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key \
     | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg; \
    NODE_MAJOR=20; \
    echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" \
     > /etc/apt/sources.list.d/nodesource.list; \
    apt-get -qy update; \
    apt-get -qy install nodejs; \
    npm install -g yarn
