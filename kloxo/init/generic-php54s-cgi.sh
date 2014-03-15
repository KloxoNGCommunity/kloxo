#!/bin/sh

export PHPRC="/opt/php54s/etc"
export PHP_INI_SCAN_DIR="/opt/php54s/etc/php.d"
php_ini="/opt/php54s/etc"

export PHP_FCGI_CHILDREN=5
export PHP_FCGI_MAX_REQUESTS=1000

if [ -f /opt/php54s/usr/bin/php-cgi ] ; then
	exec /opt/php54s/usr/bin/php-cgi -c $php_ini $*
else
	exec /opt/php54s/bin/php-cgi -c $php_ini $*
fi