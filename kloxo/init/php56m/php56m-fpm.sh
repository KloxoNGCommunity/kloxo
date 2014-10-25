#!/bin/sh

php_rc='/opt/php56m/custom/php56m.ini'
php_scan='/opt/php56m/etc/php.d'
php_prog='/opt/php56m/usr/sbin/php-fpm'

#export PHPRC=$php_rc
export PHP_INI_SCAN_DIR=$php_scan

exec $php_prog -c $php_rc $*