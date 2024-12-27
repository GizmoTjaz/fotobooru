FROM php:8.2.4-apache-bullseye

RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf
RUN a2enmod rewrite
RUN docker-php-ext-install pdo pdo_mysql
RUN echo -e "file_uploads = On\npost_max_size = 10M\nupload_max_filesize = 10M" > /usr/local/etc/php/conf.d/uploads.ini
