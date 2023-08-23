#!/bin/bash

# Klonen des Repositories
git clone https://github.com/luzumi/lottozahlengenerator.git /path/to/clone

# In das Verzeichnis wechseln
# shellcheck disable=SC2164
cd /var/www/html/lotto

# Migrationen durchführen
php artisan migrate

# Datenbank seeden, wenn leer
if [ -z "$(php artisan db:seed --dry-run)" ]; then
  php artisan db:seed
fi
