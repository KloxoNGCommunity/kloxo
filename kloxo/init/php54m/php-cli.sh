#!/bin/sh

php_rc='/opt/php54m/custom'
php_scan='/opt/php54m/etc/php.d'
php_prog='/opt/php54m/usr/bin/php'

exec env -i PHP_INI_SCAN_DIR=$php_scan $php_prog -c $php_rc $*