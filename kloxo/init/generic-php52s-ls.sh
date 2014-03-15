#!/bin/sh

export PHPRC="/opt/php52s/etc"
export PHP_INI_SCAN_DIR="/opt/php52s/etc/php.d"
php_ini="/opt/php52s/etc"

if [ -f /opt/php52s/usr/bin/php-ls ] ; then
	exec /opt/php52s/usr/bin/php-ls -c $php_ini $*
else
	exec /opt/php52s/bin/php-ls -c $php_ini $*
fi
