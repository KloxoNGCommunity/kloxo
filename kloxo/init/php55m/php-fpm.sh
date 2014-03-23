#!/bin/sh

export PHPRC="/opt/php55m/custom"
export PHP_INI_SCAN_DIR="/opt/php55m/etc/php.d"
php_ini="/opt/php55m/custom/php.ini"

exec /opt/php55m/usr/sbin/php-fpm -c $php_ini $*