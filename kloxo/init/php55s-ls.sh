#!/bin/sh

export PHPRC="/usr/local/lxlabs/kloxo/init/php55s"
export PHP_INI_SCAN_DIR="/opt/php55s/etc/php.d"
php_ini="/usr/local/lxlabs/kloxo/init/php55s"

if [ -f /opt/php55s/usr/bin/php-ls ] ; then
	exec /opt/php55s/usr/bin/php-ls -c $php_ini $*
else
	exec /opt/php55s/bin/php-ls -c $php_ini $*
fi
