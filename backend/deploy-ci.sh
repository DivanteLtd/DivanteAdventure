#!/bin/bash
set -e
echo "[deploy-ci.sh] cp ./prod-parameters.yml ./app/config/parameters.yml"
cp ./prod-parameters.yml ./app/config/parameters.yml
echo "[deploy-ci.sh] php ../../../composer.phar install --ignore-platform-reqs"
php ../../../composer.phar install --ignore-platform-reqs
echo "[deploy-ci.sh] php ./bin/console adventure:cache:clear";
php ./bin/console adventure:cache:clear
echo "[deploy-ci.sh] php ./bin/console adventure:cache:clear --env=prod";
php ./bin/console adventure:cache:clear --env=prod
echo "[deploy-ci.sh] php ./bin/console doctrine:cache:clear-result";
php ./bin/console doctrine:cache:clear-result
echo "[deploy-ci.sh] php ./bin/console doctrine:cache:clear-result --env=prod";
php ./bin/console doctrine:cache:clear-result --env=prod
echo "[deploy-ci.sh] php ./bin/console doctrine:cache:clear-metadata";
php ./bin/console doctrine:cache:clear-metadata
echo "[deploy-ci.sh] php ./bin/console doctrine:cache:clear-metadata --env=prod";
php ./bin/console doctrine:cache:clear-metadata --env=prod
echo "[deploy-ci.sh] php ./bin/console cache:clear";
php ./bin/console cache:clear
echo "[deploy-ci.sh] php ./bin/console cache:clear --env=prod";
php ./bin/console cache:clear --env=prod
echo "[deploy-ci.sh] redis-cli flushall"
redis-cli flushall
echo "[deploy-ci.sh] php ./bin/console doctrine:migrations:migrate --no-interaction";
php ./bin/console doctrine:migrations:migrate --no-interaction
