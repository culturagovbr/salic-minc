FROM php:7.0-apache

#VOLUME ["/var/www"]

RUN echo "*"
RUN apt-get update

RUN echo "[ ***** ***** ***** ] - Installing each item in new command to use cache and avoid download again ***** ***** ***** "
RUN apt-get install -y apt-utils
RUN apt-get install -y libfreetype6-dev
RUN apt-get install -y libjpeg62-turbo-dev
RUN apt-get install -y libcurl4-gnutls-dev
RUN apt-get install -y libxml2-dev
RUN apt-get install -y freetds-dev
RUN apt-get install -y git
RUN apt-get install -y curl

RUN echo "[ ***** ***** ***** ] - Installing PHP Dependencies ***** ***** ***** "
RUN docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/
RUN docker-php-ext-install gd
RUN docker-php-ext-install soap

RUN docker-php-ext-install calendar
#RUN docker-php-ext-configure mssql --with-libdir=/lib/x86_64-linux-gnu && docker-php-ext-install mssql
RUN docker-php-ext-configure pdo_dblib --with-libdir=/lib/x86_64-linux-gnu && docker-php-ext-install pdo_dblib
RUN docker-php-ext-install zip

WORKDIR /tmp/
RUN ls -la /tmp
RUN curl --silent --show-error https://getcomposer.org/installer | php
RUN ls -la /tmp/composer.phar
RUN mv /tmp/composer.phar /usr/local/bin/
RUN ln -s /usr/local/bin/composer.phar /usr/local/bin/composer

WORKDIR /var/www/

EXPOSE 80
EXPOSE 9000

COPY . /var/www/salic

RUN chmod +x -R /var/www/salic
RUN usermod -u 1000 www-data

COPY ./docker/salic-web/actions/apache2/sites-available/site.conf /etc/apache2/sites-available/site.conf
COPY ./docker/salic-web/actions/apache2/conf-available/security.conf /etc/apache2/conf-available/security.conf

RUN a2ensite site.conf
RUN a2dissite 000-default.conf
RUN a2enmod rewrite
RUN a2enmod headers
RUN cd /var/www/salic && composer install --prefer-source --no-interaction

COPY ./docker/salic-web/actions/docker-entrypoint.sh /usr/local/bin/
ENTRYPOINT ["docker-entrypoint.sh"]

CMD apachectl -DFOREGROUND
