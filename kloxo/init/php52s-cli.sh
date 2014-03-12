#!/bin/sh

if [ -f /usr/local/lxlabs/kloxo/init/softlimit ] ; then
	### MR -- must be content '/usr/bin/softlimit -m MEM' where MEM is memory in bytes
	SOFTLIMIT=$(cat /usr/local/lxlabs/kloxo/init/softlimit)
else
	SOFTLIMIT=""
fi

export PHPRC="/usr/local/lxlabs/kloxo/init/php52s"
export PHP_INI_SCAN_DIR="/opt/php52s/etc/php.d"
php_ini="/usr/local/lxlabs/kloxo/init/php52s"

if [ -f /opt/php52s/usr/bin/php ] ; then
	exec $SOFTLIMIT /opt/php52s/usr/bin/php -c $php_ini $*
else
	exec $SOFTLIMIT /opt/php52s/bin/php -c $php_ini $*
fi