#!/bin/sh

#../../vendor/bin/phpunit --debug --colors --verbose -c  ../phpunit.xml --no-coverage --testsuite Admissibilidade
docker run --rm --name=php-cli -v $(pwd)/../..:/www matriphe/alpine-php:cli php ./vendor/bin/phpunit --debug --colors --verbose -c  ./tests/phpunit.xml --no-coverage --testsuite Admissibilidade
