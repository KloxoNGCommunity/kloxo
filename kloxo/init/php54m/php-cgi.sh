#!/bin/sh

export PHPRC="/opt/php54m/custom"
export PHP_INI_SCAN_DIR="/opt/php54m/etc/php.d"
php_ini="/opt/php54m/custom/php.ini"

exec /opt/php54m/usr/bin/php-cgi -c $php_ini $*