#!/bin/sh

php_rc='/opt/php55m/custom/php.ini'
php_scan='/opt/php55m/etc/php.d'
php_prog='/opt/php55m/usr/bin/php'

#export PHPRC=$php_rc
export PHP_INI_SCAN_DIR=$php_scan

exec $php_prog -c $php_rc $*