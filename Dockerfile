FROM php:8.1-apache

# Aktivieren Sie die erforderlichen PHP-Erweiterungen
RUN apt-get update -y && \
    apt-get install -y libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev git && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd && \
    docker-php-ext-install pdo pdo_mysql && \
    docker-php-ext-install zip

# Installieren Sie den MySQL-Client
RUN apt-get update && apt-get install -y default-mysql-client

# Aktivieren Sie das Apache-Mod_rewrite
RUN a2enmod rewrite

# Klonen Sie Ihr GitHub-Repository
RUN git clone https://github.com/luzumi/lottozahlengenerator.git /var/www/html

# Installieren Sie Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Installieren Sie die Abhängigkeiten
WORKDIR /var/www/html
RUN composer install

# Setzen Sie die Berechtigungen
RUN chown -R www-data:www-data /var/www/html

# Berechtigungen für das Speicherverzeichnis
RUN chmod -R 775 /var/www/html/storage

# Setzen Sie den öffentlichen Ordner als Root
WORKDIR /var/www/html/public
