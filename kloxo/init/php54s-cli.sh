#!/bin/sh

# exec /opt/php54s/usr/bin/php -c /usr/local/lxlabs/kloxo/init/php54s/php.ini $*

if [ -f /usr/local/lxlabs/kloxo/init/softlimit ] ; then
	### MR -- must be content '/usr/bin/softlimit -m MEM' where MEM is memory in bytes
	SOFTLIMIT=$(cat /usr/local/lxlabs/kloxo/init/softlimit)
else
	SOFTLIMIT=""
fi

export PHPRC="/usr/local/lxlabs/kloxo/init/php54s"
export PHP_INI_SCAN_DIR="/opt/php54s/etc/php.d"
php_ini="/usr/local/lxlabs/kloxo/init/php54s"

if [ -f /opt/php54s/usr/bin/php ] ; then
	exec $SOFTLIMIT /opt/php54s/usr/bin/php -c $php_ini $*
else
	exec $SOFTLIMIT /opt/php54s/bin/php -c $php_ini $*
fi
