#!/bin/sh

php_rc='/etc'
php_scan='/etc/php.d'
php_prog='/usr/bin/php-cgi'

#export PHPRC=$php_rc
export PHP_INI_SCAN_DIR=$php_scan
export PHP_FCGI_CHILDREN=6
export PHP_FCGI_MAX_REQUESTS=1000

exec $php_prog -c $php_rc $*
