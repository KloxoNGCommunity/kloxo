#!/bin/sh

php_rc='/opt/php52m/custom/php.ini'
php_scan='/opt/php52m/etc/php.d'
php_prog='/opt/php52m/usr/bin/php-cgi'

#export PHPRC=$php_rc
export PHP_INI_SCAN_DIR=$php_scan
export PHP_FCGI_CHILDREN=6
export PHP_FCGI_MAX_REQUESTS=1000

exec $php_prog -c $php_rc $*
