#!/bin/bash

# Navigieren Sie zum Verzeichnis
# shellcheck disable=SC2164
cd /var/www/html/web/lotto

# Ziehen Sie den neuesten Code von Git
git pull origin master

# Installieren Sie die Abhängigkeiten
composer install

# Starten Sie den Webserver
apache2-foreground
