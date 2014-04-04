#!/bin/sh

php_rc='/opt/php52s/custom/php52s.ini'
php_scan='/opt/php52s/etc/php.d'
php_prog='/opt/php52s/usr/bin/php-cgi'

#export PHPRC=$php_rc
export PHP_INI_SCAN_DIR=$php_scan

exec $php_prog -c $php_rc $*