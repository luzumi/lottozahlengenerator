# Laden Sie die start.sh-Datei mit wget herunter
RUN wget https://raw.githubusercontent.com/luzumi/lottozahlengenerator/master/start.sh -O /start.sh

# Kopieren Sie die start.sh-Datei in den Container
COPY start.sh /start.sh

# Setzen Sie die Ausführungsberechtigungen für die start.sh-Datei
RUN chmod +x /start.sh

# Kopieren Sie das Startskript in den Container
COPY start.sh /start.sh


# Basisimage
FROM php:8.1-apache

# Installieren Sie die erforderlichen Pakete und PHP-Erweiterungen
RUN apt-get update -y && \
    apt-get install -y git libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev default-mysql-client && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql zip && \
    a2enmod rewrite

# Installieren Sie wget (falls nicht bereits vorhanden)
RUN apt-get update && apt-get install -y wget


# Installieren Sie Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Klone das Repository
RUN git clone https://github.com/luzumi/lottozahlengenerator.git /var/www/html

# Kopieren Sie die .env.example-Datei und benennen Sie sie in .env um
RUN cp .env.example .env

# Navigieren Sie zum Verzeichnis
WORKDIR /var/www/html

# Installieren Sie die Abhängigkeiten
RUN composer install

# Berechtigungen setzen
#RUN chown -R www-data:www-data /var/www/html && \
#    chmod -R 775 /var/www/html/storage

# Setzen Sie den öffentlichen Ordner als Root
WORKDIR /var/www/html/public

# Setzen Sie das Startskript als Einstiegspunkt
ENTRYPOINT ["/start.sh"]

# Nginx-Phase
FROM nginx:alpine
COPY --from=node-phase /app/frontend/build /usr/share/nginx/html
COPY --from=php-phase /app/backend /var/www/html
