#!/bin/sh
# To use your own php.ini, comment the next line and uncomment the following one
PHPRC="/etc"
export PHPRC
PHP_FCGI_CHILDREN=5
PHP_FCGI_MAX_REQUESTS=500
export PHP_FCGI_CHILDREN
export PHP_FCGI_MAX_REQUESTS
exec /usr/bin/php-cgi