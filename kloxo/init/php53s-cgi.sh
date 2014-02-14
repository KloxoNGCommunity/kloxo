#!/bin/sh
# To use your own php.ini, comment the next line and uncomment the following one
export PHP_INI_SCAN_DIR="/opt/php53s/etc/php.d"
export PHPRC="/usr/local/lxlabs/kloxo/init/php53s"
export PHP_FCGI_CHILDREN=5
export PHP_FCGI_MAX_REQUESTS=1000

if [ -f /opt/php52s/usr/bin/php ] ; then
	exec /opt/php53s/usr/bin/php-cgi
else
	exec /opt/php53s/bin/php-cgi
fi