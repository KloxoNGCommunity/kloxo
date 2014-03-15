#!/bin/sh

export PHPRC="/opt/php54s/etc"
export PHP_INI_SCAN_DIR="/opt/php54s/etc/php.d"
php_ini="/opt/php54s/etc"

if [ -f /opt/php54s/usr/sbin/php-fpm ] ; then
	/opt/php54s/usr/sbin/php-fpm -c $php_ini $*
else
	/opt/php54s/bin/php-fpm -c $php_ini $*
fi
