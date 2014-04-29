#!/bin/sh

php_rc='/opt/php54s/custom/php54s.ini'
php_scan='/opt/php54s/etc/php.d'
php_prog='/opt/php54s/usr/bin/php'

#export PHPRC=$php_rc
export PHP_INI_SCAN_DIR=$php_scan

exec $php_prog -c $php_rc $*