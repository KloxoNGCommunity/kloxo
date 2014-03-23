#!/bin/sh

export PHPRC="/opt/php52m/custom"
export PHP_INI_SCAN_DIR="/opt/php52m/etc/php.d"
php_ini="/opt/php52m/custom/php.ini"

exec /opt/php52m/usr/bin/php -c $php_ini $*