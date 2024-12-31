#!/bin/bash
composer dump-env prod
php bin/console cache:clear --no-interaction
php bin/console cache:warmup --no-interaction
php bin/console doctrine:migrations:migrate --no-interaction
rr serve -c .rr.yaml
