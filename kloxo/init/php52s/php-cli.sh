#!/bin/sh

export PHPRC="/opt/php52s/custom"
export PHP_INI_SCAN_DIR="/opt/php52s/etc/php.d"
php_ini="/opt/php52s/custom/php.ini"

exec /opt/php52s/bin/php -c $php_ini $*