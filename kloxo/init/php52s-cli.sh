#!/bin/sh

if [ -f /opt/php52s/usr/bin/php ] ; then
	exec /opt/php52s/usr/bin/php -c /usr/local/lxlabs/kloxo/init/php52s/php.ini $*
else
	exec /opt/php52s/bin/php -c /usr/local/lxlabs/kloxo/init/php52s/php.ini $*
fi