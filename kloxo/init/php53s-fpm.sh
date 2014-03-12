#!/bin/sh

export PHPRC="/usr/local/lxlabs/kloxo/init/php53s"
export PHP_INI_SCAN_DIR="/opt/php53s/etc/php.d"
php_ini="/usr/local/lxlabs/kloxo/init/php53s"

if [ -f /opt/php53s/usr/sbin/php-fpm ] ; then
	/opt/php53s/usr/sbin/php-fpm -c $php_ini $*
else
	/opt/php53s/bin/php-fpm -c $php_ini $*
fi
