#!/bin/sh

php_rc='/opt/php55m/custom'
php_scan='/opt/php55m/etc/php.d'
php_prog='/opt/php55m/usr/bin/php'

exec env -i PHP_INI_SCAN_DIR=$php_scan $php_prog -c $php_rc $*