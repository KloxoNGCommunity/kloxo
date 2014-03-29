#!/bin/sh

php_rc='/opt/php53m/custom'
php_scan='/opt/php53m/etc/php.d'
php_prog='/opt/php53m/usr/bin/php'

exec env -i PHP_INI_SCAN_DIR=$php_scan $php_prog -c $php_rc $*