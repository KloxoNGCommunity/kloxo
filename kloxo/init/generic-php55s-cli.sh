#!/bin/sh

export PHPRC="/opt/php55s/etc"
export PHP_INI_SCAN_DIR="/opt/php55s/etc/php.d"
php_ini="/opt/php55s/etc"

if [ -f /opt/php55s/usr/bin/php ] ; then
	/opt/php55s/usr/bin/php -c $php_ini $*
else
	/opt/php55s/bin/php -c $php_ini $*
fi

