#!/bin/sh

echo "Container starting..."

echo "Running database migrations..."
php artisan migrate --force

if [ $? -ne 0 ]; then
  echo "Migration failed. Stopping container."
  exit 1
fi

echo "Starting Laravel server..."
exec php artisan serve --host=0.0.0.0 --port=8000
 

 

Belangrijk is dat dit bestand LF line endings heeft.

Gebruik b.v. notepad++ om dit af te dwingen:

 



Dubbelklik op (1) Windows (CR LF) en kies voor (2) Unix (LF)

 

Onze docker containers draaien op Linux en daarom dient het entrypoint.sh script LF te gebruiken.