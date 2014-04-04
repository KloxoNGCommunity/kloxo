#!/bin/sh

php_rc='/opt/php54m/custom/php54m.ini'
php_scan='/opt/php54m/etc/php.d'
php_prog='/opt/php54m/usr/sbin/php-fpm'

#export PHPRC=$php_rc
export PHP_INI_SCAN_DIR=$php_scan

exec $php_prog -c $php_rc $*