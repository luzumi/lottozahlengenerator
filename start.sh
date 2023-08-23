#!/bin/bash

# Navigieren Sie zum Laravel-Verzeichnis
# shellcheck disable=SC2164
cd /var/www/html

# Aktualisieren Sie den Code von GitHub
git pull origin master

# Führen Sie die Migrationen durch (falls erforderlich)
php artisan migrate

# Starten Sie den Apache-Webserver
exec apache2-foreground
