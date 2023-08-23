# Basisimage
FROM php:8.1-apache

# Installieren Sie die erforderlichen Pakete und PHP-Erweiterungen
RUN apt-get update -y && \
    apt-get install -y git libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev default-mysql-client && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql zip && \
    a2enmod rewrite

# Installieren Sie Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Klone das Repository
RUN git clone https://github.com/luzumi/lottozahlengenerator.git /var/www/html

# Navigieren Sie zum Verzeichnis
WORKDIR /var/www/html/web/lotto

# Installieren Sie die Abhängigkeiten
RUN composer install

# Berechtigungen setzen
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/storage

# Führen Sie die Migrationen und das Seeding durch (Sie müssen die DB-Verbindungsdaten im .env bereitstellen)
RUN php artisan migrate --force && \
    php artisan db:seed

# Setzen Sie den öffentlichen Ordner als Root
WORKDIR /var/www/html/web/lotto/public

# Kopieren Sie das Startskript in den Container
COPY start.sh /start.sh
RUN chmod +x /start.sh

# Setzen Sie das Startskript als Einstiegspunkt
ENTRYPOINT ["/start.sh"]

# Starten Sie den Webserver
CMD ["apache2-foreground"]
