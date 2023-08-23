#!/bin/bash

# Warten Sie, bis die Datenbank verfügbar ist
while ! mysqladmin ping -h"mariadb" -P"3306" --silent; do
    sleep 1
done

# Navigieren Sie zum Laravel-Verzeichnis
# shellcheck disable=SC2164
cd /var/www/html

# Führen Sie die Migrationen und das Seeding durch
php artisan migrate --force
php artisan db:seed

# Starten Sie den Apache-Webserver
exec apache2-foreground
