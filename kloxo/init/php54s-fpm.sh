#!/bin/sh

export PHPRC="/usr/local/lxlabs/kloxo/init/php54s"
export PHP_INI_SCAN_DIR="/opt/php54s/etc/php.d"
php_ini="/usr/local/lxlabs/kloxo/init/php54s"

if [ -f /opt/php54s/usr/sbin/php-fpm ] ; then
	/opt/php54s/usr/sbin/php-fpm -c $php_ini $*
else
	/opt/php54s/bin/php-fpm -c $php_ini $*
fi
