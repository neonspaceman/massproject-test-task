FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libpq-dev \
    wget \
    zlib1g-dev \
    libmcrypt-dev \
    libzip-dev \
	zip \
	git

RUN docker-php-ext-install pdo pdo_mysql mysqli zip
RUN pecl install redis && docker-php-ext-enable redis

WORKDIR /symfony

CMD bin/console messenger:consume send-email-handler -vv