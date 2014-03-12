#!/bin/sh

export PHPRC="/usr/local/lxlabs/kloxo/init/php53s"
export PHP_INI_SCAN_DIR="/opt/php53s/etc/php.d"
php_ini="/usr/local/lxlabs/kloxo/init/php53s"

if [ -f /opt/php53s/usr/bin/php-ls ] ; then
	exec /opt/php53s/usr/bin/php-ls -c $php_ini $*
else
	exec /opt/php53s/bin/php-ls -c $php_ini $*
fi
