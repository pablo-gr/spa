#!/bin/bash
set -e

cd /spalopia

composer install
php bin/console doctrine:schema:update --force --complete
php bin/console doctrine:fixtures:load --no-interaction

php-fpm