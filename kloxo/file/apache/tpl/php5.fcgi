#!/bin/sh
export PHPRC="/etc"
export PHP_FCGI_CHILDREN=5
export PHP_FCGI_MAX_REQUESTS=1000
exec /usr/bin/php-cgi