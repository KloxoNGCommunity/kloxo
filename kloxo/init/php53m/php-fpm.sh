#!/bin/sh

export PHPRC="/opt/php53m/custom"
export PHP_INI_SCAN_DIR="/opt/php53m/etc/php.d"
php_ini="/opt/php53m/custom/php.ini"

exec /opt/php53m/usr/sbin/php-fpm -c $php_ini $*