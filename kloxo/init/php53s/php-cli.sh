#!/bin/sh

export PHPRC="/opt/php53s/custom"
export PHP_INI_SCAN_DIR="/opt/php53s/etc/php.d"
php_ini="/opt/php53s/custom/php.ini"

exec /opt/php53s/usr/bin/php -c $php_ini $*