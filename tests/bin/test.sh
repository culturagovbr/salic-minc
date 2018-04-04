#!/bin/sh

# Execução de script de teste do salic
# autor: wouerner woeurner@gmail.com
#
#../../vendor/bin/phpunit --debug --colors --verbose -c  ../phpunit.xml --testsuite Todos

docker run --rm --name=php-cli -v $(pwd)/../..:/www matriphe/alpine-php:cli php ./vendor/bin/phpunit --debug --colors --verbose -c  ./tests/phpunit.xml --testsuite Todos
