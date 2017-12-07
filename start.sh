#!/bin/sh

docker run -v `pwd`:/var/www/html -p9999:80 onjin/php-oracle
