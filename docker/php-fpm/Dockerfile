FROM php:8.3-fpm

RUN apt-get update \
    && apt-get install -y git zlib1g-dev libonig-dev libicu-dev libxrender1 libfontconfig1 libxext6 libxml2-utils libxml2-dev libzip-dev zip graphviz libsqlite3-mod-spatialite sqlite3 libmagickwand-dev \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install -o -f xdebug apcu redis pcov imagick
RUN docker-php-ext-enable xdebug apcu redis pcov imagick

# Zeitzone einrichten
ENV TZ Europe/Berlin
RUN echo $TZ > /etc/timezone && \
    apt-get update && apt-get install -y tzdata && \
    rm /etc/localtime && \
    ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install fileinfo intl mbstring opcache pdo_mysql bcmath zip xml dom sockets
RUN mkdir /var/log/php && touch /var/log/php/php_errors.log && ln -sf /dev/stderr /var/log/php/php_errors.log

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# Local PHP Security Checker
RUN curl -sSL https://github.com/fabpot/local-php-security-checker/releases/download/v2.1.3/local-php-security-checker_linux_386 --output local-php-security-checker && chmod +x local-php-security-checker

#RUN composer install && echo "eval "$(/var/www/project/bin/console completion bash)"" > /root/.bashrc

RUN apt-get update && apt-get install -y ca-certificates curl gnupg && mkdir -p /etc/apt/keyrings \
    && curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg \
    && NODE_MAJOR=20 && echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_$NODE_MAJOR.x nodistro main" | tee /etc/apt/sources.list.d/nodesource.list \
    && apt-get update && apt-get install nodejs -y

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add - \
    && echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list && apt update && apt install yarn