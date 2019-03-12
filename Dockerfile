From php:alpine
MAINTAINER bucfan83 - https://github.com/bucs-fan813

# Timezone
ENV TIMEZONE            America/New_York
ENV PHP_MEMORY_LIMIT    32M
ENV MAX_UPLOAD          10M
ENV PHP_MAX_FILE_UPLOAD 200
ENV PHP_MAX_POST        10M


# install mysql, apache and php and php extensions, tzdata, wget
#RUN echo "@community http://dl-cdn.alpinelinux.org/alpine/edge/community" >> /etc/apk/repositories && \
RUN apk update && \
    apk add \
    apache2 \
    apache2-ssl \
    mysql \
    mysql-client \
    php7-apache2 \
    php7-session \
    php7-pdo \
    php7-pdo_mysql \
    php7-mcrypt \
    php7-openssl \ 
    php7-json \
    php7-ctype \
    curl \
    php7-curl \
    wget \
    tzdata \
    nodejs \
    nodejs-npm \
	ca-certificates \
	&& update-ca-certificates \
	&& rm -rf /var/cache/apk/*
#    php5-cli \
#    php5-phar \
#    php5-zlib \
#    php5-zip \
#    php5-bz2 \
#    php5-mysqli \
#    php5-mysql \
#    php5-opcache \
#    php5-gd \
#    php5-gmp \
#    php5-dom \
#    php5-xml \
#    php5-iconv \
#    php5-xdebug




COPY etc/ssl/cert.pem /usr/local/share/ca-certificates/cert.pem

COPY etc/apache2/conf.d/ssl.conf /etc/apache2/conf.d/ssl.conf

RUN curl -sS https://getcomposer.org/installer | \
    php -- --install-dir=/usr/bin --filename=composer

WORKDIR /www

# configure timezone, mysql, apache
RUN cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
    echo "${TIMEZONE}" > /etc/timezone && \
    mkdir -p /run/mysqld && \
    chown -R mysql:mysql /run/mysqld /var/lib/mysql && \
    mysql_install_db --user=mysql --verbose=1 --basedir=/usr --datadir=/var/lib/mysql --rpm > /dev/null && \
    sed -i '/skip-external-locking/a log_error = \/var\/lib\/mysql\/error.log' /etc/mysql/my.cnf && \
    sed -i '/skip-external-locking/a general_log = ON' /etc/mysql/my.cnf && \
    sed -i '/skip-external-locking/a general_log_file = \/var\/lib\/mysql\/query.log' /etc/mysql/my.cnf && \
    ln -s /usr/lib/libxml2.so.2 /usr/lib/libxml2.so && \
    sed -i 's#\#LoadModule rewrite_module modules/mod_rewrite.so#LoadModule rewrite_module modules/mod_rewrite.so#' /etc/apache2/httpd.conf && \
    sed -i 's#AllowOverride None#AllowOverride All#' /etc/apache2/httpd.conf && \
    sed -i 's#ServerName www.example.com:80#\nServerName localhost:80#' /etc/apache2/httpd.conf && \
    sed -i 's#^DocumentRoot ".*#DocumentRoot "/www"#g' /etc/apache2/httpd.conf && \
    sed -i 's#/var/www/localhost/htdocs#/www#g' /etc/apache2/httpd.conf && \
	touch /etc/php7/php.ini && \
    sed -i "s|;*date.timezone =.*|date.timezone = ${TIMEZONE}|i" /etc/php7/php.ini && \
    sed -i "s|;*memory_limit =.*|memory_limit = ${PHP_MEMORY_LIMIT}|i" /etc/php7/php.ini && \
    sed -i "s|;*upload_max_filesize =.*|upload_max_filesize = ${MAX_UPLOAD}|i" /etc/php7/php.ini && \
    sed -i "s|;*max_file_uploads =.*|max_file_uploads = ${PHP_MAX_FILE_UPLOAD}|i" /etc/php7/php.ini && \
    sed -i "s|;*post_max_size =.*|post_max_size = ${PHP_MAX_POST}|i" /etc/php7/php.ini && \
    sed -i "s|;*cgi.fix_pathinfo=.*|cgi.fix_pathinfo= 0|i" /etc/php7/php.ini && \
    mkdir -p /run/apache2 && \
    chown -R apache:apache /run/apache2 && \
 #   mkdir /www && \
    echo "<?php phpinfo(); ?>" > /www/index.php && \
    chown -R apache:apache /www

# Configure xdebug
RUN echo "zend_extension=xdebug.so" > /etc/php7/conf.d/xdebug.ini && \ 
    echo -e "\n[XDEBUG]"  >> /etc/php7/conf.d/xdebug.ini && \ 
    echo "xdebug.remote_enable=1" >> /etc/php7/conf.d/xdebug.ini && \  
    echo "xdebug.remote_connect_back=1" >> /etc/php7/conf.d/xdebug.ini && \ 
    echo "xdebug.idekey=PHPSTORM" >> /etc/php7/conf.d/xdebug.ini && \ 
    echo "xdebug.remote_log=\"/tmp/xdebug.log\"" >> /etc/php7/conf.d/xdebug.ini

#start apache
RUN echo "#!/bin/sh" > /start.sh && \
    echo "httpd" >> /start.sh && \
    echo "nohup mysqld --skip-grant-tables --bind-address 0.0.0.0 --user mysql > /dev/null 2>&1 &" >> /start.sh && \
    echo "sleep 3 && mysql -uroot -e \"create database db;\"" >> /start.sh && \
    echo "tail -f /var/log/apache2/access.log" >> /start.sh && \
    chmod u+x /start.sh

RUN apk del tzdata

EXPOSE 80
EXPOSE 443
EXPOSE 3306

VOLUME ["/www","/var/lib/mysql","/etc/mysql/"]
ENTRYPOINT ["/start.sh"]