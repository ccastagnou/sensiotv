FROM php:8.2-fpm-bullseye

RUN apt-get update -y && \
    docker-php-ext-install \
        zip \
        pdo \
        pdo_sqlite \
        ctype \
        filter \
        iconv \
        intl \
        json \
        mbstring \
        openssl \
        session \
        simplexml \
        sodium \
        tokenizer \
        xml \
        xmlwriter \
        xsl

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
RUN apt-get install -y nodejs sqlite3
RUN npm install -g yarn
