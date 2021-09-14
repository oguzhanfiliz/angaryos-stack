FROM php:7.4-fpm

RUN apt-get update
RUN apt-get install -y --no-install-recommends \ 
    libpq-dev  libmemcached-dev  zlib1g-dev  curl  git  nano  libxml2-dev libldap2-dev \
    python3 python3-pip libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
    gconf-service libasound2 libatk1.0-0 libcairo2 libcups2 libfontconfig1 \
    libgdk-pixbuf2.0-0 libgtk-3-0 libnspr4 libpango-1.0-0 libxss1 fonts-liberation libappindicator1 \
    libnss3 lsb-release xdg-utils wget docker.io sudo gnupg unixodbc-dev libgssapi-krb5-2 \
    libssl-dev libcurl4-openssl-dev tcpdump iputils-ping net-tools libzip-dev 

WORKDIR /var/www

COPY google-chrome-80_amd64.deb /var/www/google-chrome-80_amd64.deb
RUN dpkg -i google-chrome-80_amd64.deb; apt-get -fy install

RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd

RUN pecl install -D 'enable-sockets="no" enable-openssl="yes" enable-http2="yes" enable-mysqlnd="yes" enable-swoole-json="no" enable-swoole-curl="yes" enable-cares="yes"' swoole
RUN echo "extension=swoole.so" > /usr/local/etc/php/conf.d/docker-php-ext-swoole.ini

RUN curl -L -o /tmp/memcached.tar.gz "https://github.com/php-memcached-dev/php-memcached/archive/master.tar.gz" \
    && mkdir -p /usr/src/php/ext/memcached \
    && tar -C /usr/src/php/ext/memcached -zxvf /tmp/memcached.tar.gz --strip 1 \
    && docker-php-ext-configure memcached \
    && docker-php-ext-install memcached \
    && rm /tmp/memcached.tar.gz

RUN docker-php-ext-configure ldap --with-libdir=lib/x86_64-linux-gnu/

RUN docker-php-ext-install pgsql pdo pdo_pgsql pdo_mysql sockets opcache zip xml ldap soap

RUN pip3 install selenium beautifulsoup4 lxml

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www && echo "www:www" | chpasswd


RUN chown www:www /var/www

RUN adduser www sudo

USER www

RUN chmod 777 ~/.bashrc
RUN ~/.bashrc

EXPOSE 9000
CMD ["php-fpm"]
