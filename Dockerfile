# Dockerfile

FROM php:8.1-apache

# Aktivieren Sie die erforderlichen PHP-Erweiterungen
RUN apt-get update -y && \
    apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd && \
    docker-php-ext-install pdo pdo_mysql

# Aktivieren Sie das Apache-Mod_rewrite
RUN a2enmod rewrite

# Kopieren Sie Ihre Laravel-App in den Container
COPY ./ /var/www/html/

# Setzen Sie die Berechtigungen
RUN chown -R www-data:www-data /var/www/html

# Installieren Sie Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installieren Sie die Abhängigkeiten
RUN composer install

# Berechtigungen für das Speicherverzeichnis
RUN chmod -R 775 /var/www/html/storage

# Setzen Sie den öffentlichen Ordner als Root
WORKDIR /var/www/html/public
