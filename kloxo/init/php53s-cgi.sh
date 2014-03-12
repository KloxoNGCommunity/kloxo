#!/bin/sh

export PHPRC="/usr/local/lxlabs/kloxo/init/php53s"
export PHP_INI_SCAN_DIR="/opt/php53s/etc/php.d"
php_ini="/usr/local/lxlabs/kloxo/init/php53s"

export PHP_FCGI_CHILDREN=5
export PHP_FCGI_MAX_REQUESTS=1000

if [ -f /opt/php53s/usr/bin/php-cgi ] ; then
	exec /opt/php53s/usr/bin/php-cgi -c $php_ini $*
else
	exec /opt/php53s/bin/php-cgi -c $php_ini $*
fi